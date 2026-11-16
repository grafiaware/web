<?php

namespace Events\Middleware\Events\Controler\Lottery;

use Auth\Service\Qr\QrImageGenerator;
use Events\Service\Lottery\LotteryPaperService;
use Events\Service\Lottery\LotteryService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Status\Model\Enum\FlashSeverityEnum;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

/**
 * D1c (#108): papírové anketní lístky – vytvoření sady, tisková stránka (A4, lístek + ústřižek s velkým číslem a QR),
 * export CSV pro tisk štítků, přehled a vyhledání lístku podle čísla. Oprávnění: LotteryControlerAbstract.
 */
class LotteryPaperControler extends LotteryControlerAbstract {

    /** Nejvýš lístků na jednu tiskovou stránku (QR se generují při vykreslení). */
    public const MAX_PRINT = 400;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            LotteryService $lotteries,
            private LotteryPaperService $papers,
            private QrImageGenerator $qr) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo, $lotteries);
    }

    /** Přehled lístků, vyhledání podle čísla (obsluha). */
    public function overview(ServerRequestInterface $request, $id): ResponseInterface {
        if (!$this->isStaff()) {
            return $this->deniedPage($request);
        }
        $lottery = $this->lotteries->lottery((int) $id);
        if ($lottery === null) {
            return $this->page($request, 'Losování', 'text.php', ['text' => 'Losování neexistuje.'], 404);
        }
        $q = trim((string) $this->param($request, 'q', ''));
        $found = null;
        if ($q !== '') {
            $number = LotteryPaperService::numberFromText($q);
            $code = LotteryPaperService::codeFromScannedText($q);
            $ticket = $number !== null ? $this->papers->byNumber((int) $id, $number) : ($code !== null ? $this->papers->byCode($code) : null);
            $found = $ticket !== null && (int) $ticket['lottery_id_fk'] === (int) $id ? $this->papers->status($ticket) : false;
        }
        return $this->page($request, 'Papírové lístky', 'paper.php', [
            'lottery' => $lottery,
            'stats' => $this->papers->stats((int) $id),
            'q' => $q,
            'found' => $found,
            'isAdmin' => $this->isAdmin(),
            'perPage' => $this->lotteries->config()->paperPerPage,
            'maxSet' => LotteryPaperService::MAX_SET,
            'maxPrint' => self::MAX_PRINT,
        ]);
    }

    /** POST vytvoření další sady lístků (počet). */
    public function createSet(ServerRequestInterface $request, $id): ResponseInterface {
        $back = "events/v1/lottery/{$id}/paper";
        if (!$this->isAdmin()) {
            $this->addFlashMessage('Jen pro správce losování.', FlashSeverityEnum::WARNING);
            return $this->createResponseRedirectSeeOther($request, $back);
        }
        if ($this->lotteries->lottery((int) $id) === null) {
            return $this->createResponseRedirectSeeOther($request, 'events/v1/lottery');
        }
        try {
            $set = $this->papers->createSet((int) $id, (int) $this->param($request, 'count', 0));
            $this->addFlashMessage('Vytvořeny lístky ' . LotteryPaperService::formatNumber($set['from']) . '–'
                . LotteryPaperService::formatNumber($set['to']) . '.', FlashSeverityEnum::SUCCESS);
        } catch (\InvalidArgumentException $e) {
            $this->addFlashMessage($e->getMessage(), FlashSeverityEnum::WARNING);
        }
        return $this->createResponseRedirectSeeOther($request, $back);
    }

    /** Tisková stránka (A4, „Uložit jako PDF“ v prohlížeči). Rozsah ?from=&to=, počet na stránku ?per=4|2. */
    public function printPage(ServerRequestInterface $request, $id): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->deniedPage($request, 'Tisk lístků je jen pro správce losování.');
        }
        $lottery = $this->lotteries->lottery((int) $id);
        if ($lottery === null) {
            return $this->page($request, 'Losování', 'text.php', ['text' => 'Losování neexistuje.'], 404);
        }
        [$from, $to] = $this->range($request);
        $tickets = $this->papers->tickets((int) $id, $from, $to);
        if (count($tickets) > self::MAX_PRINT) {
            return $this->page($request, 'Tisk lístků', 'text.php', ['text' => 'Vyberte nejvýš ' . self::MAX_PRINT
                . ' lístků najednou (rozsah od–do).'], 400);
        }
        $per = (int) $this->param($request, 'per', $this->lotteries->config()->paperPerPage);
        foreach ($tickets as &$t) {
            $url = $this->absoluteUrl($request, 'events/v1/scan/' . LotteryPaperService::scanCode($t['code']));
            $t['qr'] = 'data:image/png;base64,' . base64_encode($this->qr->generate($url)->getString());
            $t['label'] = LotteryPaperService::formatNumber((int) $t['number']);
        }
        unset($t);
        return $this->page($request, 'Lístky ' . $lottery['name'], 'paper-print.php', [
            'lottery' => $lottery,
            'tickets' => $tickets,
            'questions' => $this->lotteries->questions((int) $id),
            'per' => in_array($per, [2, 4], true) ? $per : 4,
            'registerUrl' => $this->absoluteUrl($request, ''),
        ], 200, '_print.php');
    }

    /** Export CSV (číslo, kód, adresa QR) pro vlastní aplikaci na tisk štítků. */
    public function csv(ServerRequestInterface $request, $id): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->deniedPage($request);
        }
        [$from, $to] = $this->range($request);
        $out = fopen('php://temp', 'w+');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['Číslo', 'Kód', 'Adresa QR'], ';', '"', '\\');
        foreach ($this->papers->tickets((int) $id, $from, $to) as $t) {
            fputcsv($out, [LotteryPaperService::formatNumber((int) $t['number']), LotteryPaperService::scanCode($t['code']),
                $this->absoluteUrl($request, 'events/v1/scan/' . LotteryPaperService::scanCode($t['code']))], ';', '"', '\\');
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);
        return $this->createStringOKResponse($csv)
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="losovani-' . (int) $id . '-listky.csv"')
            ->withHeader('Cache-Control', 'no-store');
    }

    /** @return array{0: ?int, 1: ?int} */
    private function range(ServerRequestInterface $request): array {
        $from = (int) $this->param($request, 'from', 0);
        $to = (int) $this->param($request, 'to', 0);
        return [$from > 0 ? $from : null, $to > 0 ? $to : null];
    }
}
