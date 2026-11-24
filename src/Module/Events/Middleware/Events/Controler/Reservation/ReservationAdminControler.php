<?php

namespace Events\Middleware\Events\Controler\Reservation;

use Events\Service\Lottery\LotteryService;
use Events\Service\Reservation\ReservationMailer;
use Events\Service\Reservation\SchoolReservationService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Status\Model\Enum\FlashSeverityEnum;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

/**
 * R1 (#48): administrace rezervací pro školy (events_administrator) – nabídka, výběr přednášek, přehled po přednáškách
 * s telefony pedagogů, úprava / přesun / zrušení rezervace i po uzávěrce, export CSV bez telefonů, mazání telefonů,
 * anonymizace. Kapacitu přednášek nastavuje správa eventů (G0, vp/event).
 */
class ReservationAdminControler extends ReservationControlerAbstract {

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            SchoolReservationService $reservations,
            private ReservationMailer $mailer) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo, $reservations);
    }

    private function denied(ServerRequestInterface $request): ResponseInterface {
        return $this->textPage($request, 'Přístup odepřen', 'Tato stránka je jen pro správce rezervací. Přihlaste se účtem správce programu.', 403);
    }

    /** POST (formulář) smí odpovědět jen přesměrováním – hláška a 303. */
    private function deniedPost(ServerRequestInterface $request): ResponseInterface {
        $this->addFlashMessage('Rezervace může spravovat jen správce programu.', FlashSeverityEnum::WARNING);
        return $this->createResponseRedirectSeeOther($request, 'events/v1/reservation');
    }

    private function notFoundPost(ServerRequestInterface $request, string $text): ResponseInterface {
        $this->addFlashMessage($text, FlashSeverityEnum::WARNING);
        return $this->createResponseRedirectSeeOther($request, 'events/v1/reservation');
    }

    public function index(ServerRequestInterface $request): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->denied($request);
        }
        return $this->page($request, 'Rezervace pro školy', 'admin-list.php', ['offers' => $this->reservations->offers(),
            'year' => (int) $this->reservations->now()->format('Y') + 1]);
    }

    public function detail(ServerRequestInterface $request, $id): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->denied($request);
        }
        $offer = $this->reservations->offer((int) $id);
        if ($offer === null) {
            return $this->textPage($request, 'Rezervace pro školy', 'Nabídka neexistuje.', 404);
        }
        $reservations = $this->reservations->reservationsOfOffer((int) $id, true);
        $byEvent = [];
        foreach ($reservations as $r) {
            $byEvent[(int) $r['event_id_fk']][] = $r;
        }
        return $this->page($request, $offer['name'], 'admin-offer.php', [
            'offer' => $offer,
            'state' => SchoolReservationService::bookingState($offer, $this->reservations->now()),
            'candidates' => $this->reservations->lectureCandidates($offer),
            'lectures' => $this->reservations->lectures((int) $id),
            'byEvent' => $byEvent,
            'phones' => count(array_filter($reservations, fn($r) => $r['contact_phone'] !== null)),
            'editId' => (int) $this->param($request, 'edit', 0),
        ]);
    }

    public function saveOffer(ServerRequestInterface $request, $id = null): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->deniedPost($request);
        }
        $data = [];
        foreach (['code', 'name', 'event_date', 'status'] as $k) {
            $data[$k] = (string) $this->param($request, $k, '');
        }
        foreach (['booking_from' => '00:00', 'booking_until' => '23:59', 'change_until' => '23:59'] as $k => $defaultTime) {
            $data[$k] = LotteryService::dtFromParts((string) $this->param($request, $k . '_date', ''), (string) $this->param($request, $k . '_time', ''), $defaultTime) ?? '';
        }
        if ($id !== null && $this->reservations->offer((int) $id) === null) {
            return $this->notFoundPost($request, 'Nabídka neexistuje.');
        }
        $result = $this->reservations->saveOffer($id === null ? null : (int) $id, $data);
        foreach ($result['errors'] as $error) {
            $this->addFlashMessage($error, FlashSeverityEnum::WARNING);
        }
        if ($result['errors']) {
            return $this->createResponseRedirectSeeOther($request, $id === null ? 'events/v1/reservation' : "events/v1/reservation/offer/{$id}");
        }
        $this->addFlashMessage('Nabídka uložena.', FlashSeverityEnum::SUCCESS);
        return $this->createResponseRedirectSeeOther($request, "events/v1/reservation/offer/{$result['id']}");
    }

    public function saveOfferEvents(ServerRequestInterface $request, $id): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->deniedPost($request);
        }
        if ($this->reservations->offer((int) $id) === null) {
            return $this->notFoundPost($request, 'Nabídka neexistuje.');
        }
        $ids = $this->param($request, 'events', []);
        $kept = $this->reservations->setOfferEvents((int) $id, is_array($ids) ? $ids : []);
        $this->addFlashMessage('Výběr přednášek uložen.', FlashSeverityEnum::SUCCESS);
        if ($kept) {
            $this->addFlashMessage('Přednášky s aktivními rezervacemi zůstaly v nabídce (nejdřív rezervace přesuňte nebo zrušte).', FlashSeverityEnum::WARNING);
        }
        return $this->createResponseRedirectSeeOther($request, "events/v1/reservation/offer/{$id}");
    }

    public function saveReservation(ServerRequestInterface $request, $rid): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->deniedPost($request);
        }
        $before = $this->reservations->reservation((int) $rid);
        if ($before === null) {
            return $this->notFoundPost($request, 'Rezervace neexistuje.');
        }
        $result = $this->reservations->updateByAdmin((int) $rid, $this->reservationInput($request), $this->loginName());
        $back = "events/v1/reservation/offer/{$before['offer_id_fk']}";
        if (!$result['ok']) {
            foreach ($result['errors'] as $error) {
                $this->addFlashMessage($error, FlashSeverityEnum::WARNING);
            }
            return $this->createResponseRedirectSeeOther($request, $back . '?edit=' . (int) $rid . '#r' . (int) $rid);
        }
        foreach ($result['warnings'] ?? [] as $warning) {
            $this->addFlashMessage($warning, FlashSeverityEnum::WARNING);
        }
        $after = $this->reservations->reservation((int) $rid);
        if ($this->param($request, 'notify', false) && $after['status'] === SchoolReservationService::ACTIVE) {
            $this->mail(fn() => $this->mailer->sendToPedagog(ReservationMailer::CHANGED_BY_ORGANIZER, $after));
        }
        $this->addFlashMessage('Rezervace uložena.', FlashSeverityEnum::SUCCESS);
        return $this->createResponseRedirectSeeOther($request, $back . '#e' . (int) $after['event_id_fk']);
    }

    public function cancelReservation(ServerRequestInterface $request, $rid): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->deniedPost($request);
        }
        $r = $this->reservations->reservation((int) $rid);
        if ($r === null) {
            return $this->notFoundPost($request, 'Rezervace neexistuje.');
        }
        $result = $this->reservations->cancelByAdmin((int) $rid, $this->loginName());
        if ($result['ok']) {
            if ($this->param($request, 'notify', false)) {
                $this->mail(fn() => $this->mailer->sendToPedagog(ReservationMailer::CANCELLED_BY_ORGANIZER, $r));
            }
            $this->addFlashMessage('Rezervace zrušena.', FlashSeverityEnum::SUCCESS);
        } else {
            foreach ($result['errors'] as $error) {
                $this->addFlashMessage($error, FlashSeverityEnum::WARNING);
            }
        }
        return $this->createResponseRedirectSeeOther($request, "events/v1/reservation/offer/{$r['offer_id_fk']}#e" . (int) $r['event_id_fk']);
    }

    public function deletePhones(ServerRequestInterface $request, $id): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->deniedPost($request);
        }
        $n = $this->reservations->deletePhones((int) $id);
        $this->addFlashMessage("Smazáno telefonů: {$n}.", FlashSeverityEnum::SUCCESS);
        return $this->createResponseRedirectSeeOther($request, "events/v1/reservation/offer/{$id}");
    }

    public function anonymize(ServerRequestInterface $request, $id): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->deniedPost($request);
        }
        $n = $this->reservations->anonymize((int) $id);
        if ($n === null) {
            $this->addFlashMessage('Anonymizovat lze až po skončení dne akce.', FlashSeverityEnum::WARNING);
        } else {
            $this->addFlashMessage("Anonymizováno rezervací: {$n}. Škola, obor, třída a počty zůstaly pro statistiku.", FlashSeverityEnum::SUCCESS);
        }
        return $this->createResponseRedirectSeeOther($request, "events/v1/reservation/offer/{$id}");
    }

    /** Export aktivních rezervací (CSV, UTF-8 s BOM, středník), bez telefonů. */
    public function csv(ServerRequestInterface $request, $id): ResponseInterface {
        if (!$this->isAdmin()) {
            return $this->denied($request);
        }
        $offer = $this->reservations->offer((int) $id);
        if ($offer === null) {
            return $this->textPage($request, 'Rezervace pro školy', 'Nabídka neexistuje.', 404);
        }
        $out = fopen('php://temp', 'w+');
        fwrite($out, "\xEF\xBB\xBF");
        foreach ($this->reservations->csvRows((int) $id) as $row) {
            fputcsv($out, $row, ';', '"', '\\');
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);
        return $this->createStringOKResponse($csv)
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="rezervace-' . $offer['code'] . '.csv"')
            ->withHeader('Cache-Control', 'no-store');
    }

    private function mail(callable $send): void {
        try {
            $send();
        } catch (\Throwable $e) {
            error_log('R1 reservation mail failed: ' . get_class($e) . ': ' . $e->getMessage()
                . ($e->getPrevious() ? ' / ' . $e->getPrevious()->getMessage() : ''));
        }
    }
}
