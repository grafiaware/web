<?php

namespace Events\Middleware\Events\Controler\Lottery;

use Events\Service\Lottery\AuthLookup;
use Events\Service\Lottery\LotteryDrawService;
use Events\Service\Lottery\LotteryScanService;
use Events\Service\Lottery\LotteryService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Status\Model\Enum\FlashSeverityEnum;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

/**
 * D1 (#46): obsluha na místě – skener (vchod, infostánek), ruční vydání / prodloužení, losování kola, převzetí, náhradník.
 * Stránky jsou upravené pro telefon. Oprávnění: LotteryControlerAbstract::isStaff().
 */
class LotteryStaffControler extends LotteryControlerAbstract {

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            LotteryService $lotteries,
            private LotteryScanService $scanService,
            private LotteryDrawService $draws,
            private AuthLookup $authLookup) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo, $lotteries);
    }

    ### skener ##########################################################################

    public function scanner(ServerRequestInterface $request, $id): ResponseInterface {
        if (!$this->isStaff()) {
            return $this->deniedPage($request);
        }
        $lottery = $this->lotteries->lottery((int) $id);
        if ($lottery === null) {
            return $this->page($request, 'Losování', 'text.php', ['text' => 'Losování neexistuje.'], 404);
        }
        $stations = $this->lotteries->config()->stations;
        $station = (string) $this->param($request, 'station', array_key_first($stations));
        if (!isset($stations[$station])) {
            $station = (string) array_key_first($stations);
        }
        return $this->page($request, 'Skener', 'scanner.php', [
            'lottery' => $lottery,
            'stations' => array_map(fn($s) => $s + ['label' => '', 'issue' => false], $stations),
            'station' => $station,
            'jsQrUrl' => 'public/site/common/js/lib/jsQR-1.4.0.min.js',
        ]);
    }

    /** POST sken (JSON pro skener). */
    public function scan(ServerRequestInterface $request, $id): ResponseInterface {
        if (!$this->isStaff()) {
            return $this->createJsonOKResponse(['ok' => false, 'result' => 'denied', 'message' => 'Nejste přihlášeni jako obsluha.', 'name' => '']);
        }
        $res = $this->scanService->scan((int) $id, (string) $this->param($request, 'code', ''),
            (string) $this->param($request, 'station', ''), $this->param($request, 'scannedAt'), $this->loginName());
        unset($res['login']);
        return $this->createJsonOKResponse($res);
    }

    /** GET vyhledání návštěvníka (JSON) – záloha ke skeneru. */
    public function searchVisitor(ServerRequestInterface $request, $id): ResponseInterface {
        if (!$this->isStaff()) {
            return $this->createJsonOKResponse([]);
        }
        $q = trim((string) $this->param($request, 'q', ''));
        $byEmail = str_contains($q, '@') ? $this->authLookup->loginsByEmail($q) : [];
        return $this->createJsonOKResponse($this->lotteries->searchVisitors((int) $id, $q, $byEmail));
    }

    /** POST ruční vydání (issue=1) nebo prodloužení (issue=0). */
    public function presence(ServerRequestInterface $request, $id): ResponseInterface {
        $return = $this->safeReturn((string) $this->param($request, 'return', "events/v1/lottery/{$id}/scanner"));
        if (!$this->isStaff()) {
            $this->addFlashMessage('Jen pro obsluhu losování.', FlashSeverityEnum::WARNING);
            return $this->createResponseRedirectSeeOther($request, $return);
        }
        $login = (string) $this->param($request, 'login', '');
        $res = $this->scanService->manual((int) $id, $login, (string) $this->param($request, 'issue', '0') === '1', $this->loginName());
        $this->addFlashMessage(trim($res['name'] . ': ' . $res['message'], ': '), $res['ok'] ? FlashSeverityEnum::SUCCESS : FlashSeverityEnum::WARNING);
        return $this->createResponseRedirectSeeOther($request, $return);
    }

    ### losování kola ###################################################################

    public function roundPage(ServerRequestInterface $request, $roundId): ResponseInterface {
        if (!$this->isStaff()) {
            return $this->deniedPage($request);
        }
        $round = $this->lotteries->round((int) $roundId);
        if ($round === null) {
            return $this->page($request, 'Kolo', 'text.php', ['text' => 'Kolo neexistuje.'], 404);
        }
        $lottery = $this->lotteries->lottery((int) $round['lottery_id_fk']);
        $participants = $this->draws->participants($round);
        return $this->page($request, 'Losování kola', 'round.php', [
            'lottery' => $lottery,
            'round' => $round,
            'rounds' => $this->lotteries->rounds((int) $lottery['id']),
            'participants' => count($participants),
            'tickets' => array_sum($participants),
            'prizes' => $this->draws->availablePrizes($round),
            'winners' => $this->draws->winnersOfRound((int) $round['id']),
        ]);
    }

    public function draw(ServerRequestInterface $request, $roundId): ResponseInterface {
        $back = "events/v1/lottery/round/{$roundId}";
        if (!$this->isStaff()) {
            $this->addFlashMessage('Jen pro obsluhu losování.', FlashSeverityEnum::WARNING);
            return $this->createResponseRedirectSeeOther($request, $back);
        }
        $prizeId = (int) $this->param($request, 'prize', 0);
        if ($this->param($request, 'all')) {
            $won = $this->draws->drawAll((int) $roundId, $this->loginName());
            $this->addFlashMessage($won ? 'Vylosováno výher: ' . count($won) . '.' : 'Není co nebo z koho losovat.',
                $won ? FlashSeverityEnum::SUCCESS : FlashSeverityEnum::WARNING);
        } else {
            $w = $this->draws->drawOne((int) $roundId, $prizeId > 0 ? $prizeId : null, $this->loginName());
            if ($w === null) {
                $this->addFlashMessage('Není co nebo z koho losovat (žádná cena nebo žádný účastník s platným losem).', FlashSeverityEnum::WARNING);
            } else {
                $this->addFlashMessage("Vylosován(a): {$w['display_name']} – {$w['prize_name']}", FlashSeverityEnum::SUCCESS);
            }
        }
        return $this->createResponseRedirectSeeOther($request, $back);
    }

    public function claimed(ServerRequestInterface $request, $winnerId): ResponseInterface {
        $back = $this->safeReturn((string) $this->param($request, 'return', 'events/v1/lottery'));
        if (!$this->isStaff()) {
            $this->addFlashMessage('Jen pro obsluhu losování.', FlashSeverityEnum::WARNING);
            return $this->createResponseRedirectSeeOther($request, $back);
        }
        $office = $this->param($request, 'place', 'misto') === 'sidlo';
        $ok = $this->draws->claim((int) $winnerId, $office, $this->loginName());
        $this->addFlashMessage($ok ? ($office ? 'Zapsáno: převzal v sídle.' : 'Zapsáno: převzal na místě.') : 'Výhru nelze označit (už převzatá nebo propadlá).',
            $ok ? FlashSeverityEnum::SUCCESS : FlashSeverityEnum::WARNING);
        return $this->createResponseRedirectSeeOther($request, $back);
    }

    public function substitute(ServerRequestInterface $request, $winnerId): ResponseInterface {
        $winner = $this->draws->winner((int) $winnerId);
        $back = $winner !== null && $winner['round_id_fk'] !== null ? "events/v1/lottery/round/{$winner['round_id_fk']}" : 'events/v1/lottery';
        if (!$this->isStaff()) {
            $this->addFlashMessage('Jen pro obsluhu losování.', FlashSeverityEnum::WARNING);
            return $this->createResponseRedirectSeeOther($request, $back);
        }
        $res = $this->draws->substitute((int) $winnerId, $this->loginName());
        if (!$res['forfeited']) {
            $this->addFlashMessage('Náhradníka lze losovat jen v kole s náhradníkem a jen u nepřevzaté výhry.', FlashSeverityEnum::WARNING);
        } elseif ($res['winner'] === null) {
            $this->addFlashMessage('Výhra propadla, ale není z koho losovat náhradníka.', FlashSeverityEnum::WARNING);
        } else {
            $this->addFlashMessage("Náhradník: {$res['winner']['display_name']} – {$res['winner']['prize_name']}", FlashSeverityEnum::SUCCESS);
        }
        return $this->createResponseRedirectSeeOther($request, $back);
    }

    /** Návratová adresa jen v rámci losování (ne cizí web). */
    private function safeReturn(string $return): string {
        $return = ltrim($return, '/');
        return preg_match('~^events/v1/(lottery|scan)/[A-Za-z0-9/_?=&.%-]*$~', $return) ? $return : 'events/v1/lottery';
    }
}
