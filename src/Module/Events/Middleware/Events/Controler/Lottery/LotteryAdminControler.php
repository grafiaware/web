<?php

namespace Events\Middleware\Events\Controler\Lottery;

use Events\Service\Lottery\AuthLookup;
use Events\Service\Lottery\LotteryDrawService;
use Events\Service\Lottery\LotteryPaperService;
use Events\Service\Lottery\LotteryService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Status\Model\Enum\FlashSeverityEnum;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

/**
 * D1 (#46): nastavení losování (před akcí) – losování, rozpis kol, ceny, anketa; přehled výher a export nevyzvednutých.
 * Oprávnění: LotteryControlerAbstract::isAdmin() (zatím events_administrator).
 */
class LotteryAdminControler extends LotteryControlerAbstract {

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            LotteryService $lotteries,
            private LotteryDrawService $draws,
            private AuthLookup $authLookup) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo, $lotteries);
    }

    public function index(ServerRequestInterface $request): ResponseInterface {
        if (!$this->isStaff()) {
            return $this->deniedPage($request);
        }
        return $this->page($request, 'Losování', 'admin-list.php', ['lotteries' => $this->lotteries->lotteries(), 'isAdmin' => $this->isAdmin()]);
    }

    public function detail(ServerRequestInterface $request, $id): ResponseInterface {
        if (!$this->isStaff()) {
            return $this->deniedPage($request);
        }
        $lottery = $this->lotteries->lottery((int) $id);
        if ($lottery === null) {
            return $this->page($request, 'Losování', 'text.php', ['text' => 'Losování neexistuje.'], 404);
        }
        $lid = (int) $lottery['id'];
        return $this->page($request, $lottery['name'], 'admin-lottery.php', [
            'lottery' => $lottery,
            'rounds' => $this->lotteries->rounds($lid),
            'prizes' => $this->lotteries->prizes($lid),
            'questions' => $this->lotteries->questions($lid),
            'stations' => $this->lotteries->config()->stations,
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    public function saveLottery(ServerRequestInterface $request, $id = null): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->denied($request);
        }
        $lid = $this->lotteries->saveLottery($id === null ? null : (int) $id, $this->fields($request, ['name', 'description', 'status', 'join_from', 'join_until', 'rules_url']));
        $this->addFlashMessage('Losování uloženo.', FlashSeverityEnum::SUCCESS);
        return $this->createResponseRedirectSeeOther($request, "events/v1/lottery/{$lid}");
    }

    public function saveRound(ServerRequestInterface $request, $id, $roundId = null): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->denied($request);
        }
        $ok = $this->lotteries->saveRound((int) $id, $roundId === null ? null : (int) $roundId,
            (string) $this->param($request, 'draw_at', ''), (bool) $this->param($request, 'substitute', false));
        $this->addFlashMessage($ok ? 'Kolo uloženo.' : 'Zadejte datum a čas kola.', $ok ? FlashSeverityEnum::SUCCESS : FlashSeverityEnum::WARNING);
        return $this->createResponseRedirectSeeOther($request, "events/v1/lottery/{$id}");
    }

    public function removeRound(ServerRequestInterface $request, $id, $roundId): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->denied($request);
        }
        $ok = $this->lotteries->removeRound((int) $roundId);
        $this->addFlashMessage($ok ? 'Kolo smazáno.' : 'Vylosované kolo nelze smazat.', $ok ? FlashSeverityEnum::SUCCESS : FlashSeverityEnum::WARNING);
        return $this->createResponseRedirectSeeOther($request, "events/v1/lottery/{$id}");
    }

    public function savePrize(ServerRequestInterface $request, $id, $prizeId = null): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->denied($request);
        }
        $this->lotteries->savePrize((int) $id, $prizeId === null ? null : (int) $prizeId, $this->fields($request, ['name', 'quantity', 'priority', 'round_id_fk']));
        $this->addFlashMessage('Cena uložena.', FlashSeverityEnum::SUCCESS);
        return $this->createResponseRedirectSeeOther($request, "events/v1/lottery/{$id}");
    }

    public function removePrize(ServerRequestInterface $request, $id, $prizeId): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->denied($request);
        }
        $ok = $this->lotteries->removePrize((int) $prizeId);
        $this->addFlashMessage($ok ? 'Cena smazána.' : 'Vylosovanou cenu nelze smazat.', $ok ? FlashSeverityEnum::SUCCESS : FlashSeverityEnum::WARNING);
        return $this->createResponseRedirectSeeOther($request, "events/v1/lottery/{$id}");
    }

    public function saveQuestion(ServerRequestInterface $request, $id, $questionId = null): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->denied($request);
        }
        $this->lotteries->saveQuestion((int) $id, $questionId === null ? null : (int) $questionId,
            $this->fields($request, ['text', 'type', 'options', 'required', 'position']));
        $this->addFlashMessage('Otázka uložena.', FlashSeverityEnum::SUCCESS);
        return $this->createResponseRedirectSeeOther($request, "events/v1/lottery/{$id}");
    }

    public function removeQuestion(ServerRequestInterface $request, $id, $questionId): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->denied($request);
        }
        $ok = $this->lotteries->removeQuestion((int) $questionId);
        $this->addFlashMessage($ok ? 'Otázka smazána.' : 'Otázku, na kterou už někdo odpověděl, nelze smazat.', $ok ? FlashSeverityEnum::SUCCESS : FlashSeverityEnum::WARNING);
        return $this->createResponseRedirectSeeOther($request, "events/v1/lottery/{$id}");
    }

    /** Přehled výher; vyhledání výherce podle e-mailu (vyzvednutí v sídle). */
    public function winners(ServerRequestInterface $request, $id): ResponseInterface {
        if (!$this->isStaff()) {
            return $this->deniedPage($request);
        }
        $lottery = $this->lotteries->lottery((int) $id);
        if ($lottery === null) {
            return $this->page($request, 'Losování', 'text.php', ['text' => 'Losování neexistuje.'], 404);
        }
        $winners = $this->draws->winnersOfLottery((int) $id);
        $q = trim((string) $this->param($request, 'q', ''));
        if ($q !== '') {
            $logins = str_contains($q, '@') ? $this->authLookup->loginsByEmail($q) : [];
            $winners = array_values(array_filter($winners, fn($w) => in_array($w['login_login_name_fk'], $logins, true)
                || ($logins === [] && (mb_stripos($w['display_name'], $q) !== false || mb_stripos((string) $w['login_login_name_fk'], $q) !== false))));
        }
        return $this->page($request, 'Výhry', 'winners.php', ['lottery' => $lottery, 'winners' => $winners, 'q' => $q]);
    }

    /** Export nevyzvednutých výher (CSV, UTF-8 s BOM, středník – otevře Excel i LibreOffice). */
    public function unclaimedCsv(ServerRequestInterface $request, $id): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->deniedPage($request);
        }
        $out = fopen('php://temp', 'w+');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['Cena', 'Kolo', 'Jméno', 'Lístek', 'Přihlašovací jméno', 'E-mail', 'Telefon', 'Vylosováno'], ';', '"', '\\');
        foreach ($this->draws->winnersOfLottery((int) $id, true) as $w) {
            $login = (string) $w['login_login_name_fk'];
            // D1c: u lístku bez e-mailu dohledá kontakt organizátor v odevzdané anketě podle čísla
            $paper = $w['paper_number'] !== null ? LotteryPaperService::formatNumber((int) $w['paper_number']) : '';
            fputcsv($out, [$w['prize_name'], $w['round_draw_at'] ?? '', $w['display_name'], $paper, $login,
                $login !== '' ? ($this->authLookup->emailOf($login) ?? '') : '', $this->lotteries->phoneOf($login), $w['drawn']], ';', '"', '\\');
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);
        return $this->createStringOKResponse($csv)
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="losovani-' . (int) $id . '-nevyzvednute.csv"')
            ->withHeader('Cache-Control', 'no-store');
    }

    private function denied(ServerRequestInterface $request): ResponseInterface {
        $this->addFlashMessage('Nastavení losování může měnit jen jeho správce.', FlashSeverityEnum::WARNING);
        return $this->createResponseRedirectSeeOther($request, 'events/v1/lottery');
    }

    private function fields(ServerRequestInterface $request, array $names): array {
        $ret = [];
        foreach ($names as $n) {
            $ret[$n] = $this->param($request, $n);
        }
        return $ret;
    }
}
