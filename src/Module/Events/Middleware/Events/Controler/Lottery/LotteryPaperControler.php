<?php

namespace Events\Middleware\Events\Controler\Lottery;

use Events\Service\Lottery\LotteryPaperService;
use FrontControler\StatusEnum;
use Events\Service\Lottery\LotteryService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Status\Model\Enum\FlashSeverityEnum;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

/**
 * D1c (#108): papírové anketní lístky – vytvoření sady, data pro PDF (PDF skládá jsPDF v prohlížeči správce,
 * public/site/common/js/lottery-paper-pdf.js), export CSV pro tisk štítků, přehled a vyhledání lístku podle čísla.
 * Oprávnění: LotteryControlerAbstract.
 */
class LotteryPaperControler extends LotteryControlerAbstract {

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            LotteryService $lotteries,
            private LotteryPaperService $papers) {
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
            'maxPdf' => LotteryPaperService::MAX_PDF,
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

    /**
     * Data pro PDF lístků (JSON): losování, otázky ankety, lístky v rozsahu ?from=&to=, adresa skenu a registrace, počet na A4.
     * PDF vytvoří jsPDF v prohlížeči; server QR negeneruje.
     */
    public function pdfData(ServerRequestInterface $request, $id): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->createJsonOKResponse(['error' => 'PDF lístků může vytvořit jen správce losování.'], StatusEnum::_403_Forbidden)
                ->withHeader('Cache-Control', 'no-store');
        }
        [$from, $to] = $this->range($request);
        try {
            $data = $this->papers->pdfData((int) $id, $from, $to);
        } catch (\InvalidArgumentException $e) {
            return $this->createJsonOKResponse(['error' => $e->getMessage()], StatusEnum::_400_BadRequest)->withHeader('Cache-Control', 'no-store');
        }
        $per = (int) $this->param($request, 'per', $this->lotteries->config()->paperPerPage);
        $data['perPage'] = in_array($per, [2, 4], true) ? $per : $this->lotteries->config()->paperPerPage;
        $data['scanBase'] = $this->absoluteUrl($request, 'events/v1/scan/');
        $data['registerHost'] = preg_replace('~^https?://~', '', rtrim($this->absoluteUrl($request, ''), '/'));
        if ($data['lottery']['rulesUrl'] !== null) {
            $data['lottery']['rulesText'] = preg_replace('~^https?://~', '', $data['lottery']['rulesUrl']);
        }
        return $this->createJsonOKResponse($data)->withHeader('Cache-Control', 'no-store');
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
