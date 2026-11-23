<?php

namespace Events\Middleware\Events\Controler\Reservation;

use Events\Service\Reservation\ReservationMailer;
use Events\Service\Reservation\SchoolReservationService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Status\Model\Enum\FlashSeverityEnum;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

/**
 * R1 (#48): pedagog – rezervace z veřejného formuláře (bez přihlášení) a správa rezervace odkazem z e-mailu.
 *
 * POST /events/v1/reservation/book/:code              nová rezervace (formulář na statické stránce vp/rezervace-skol)
 * GET  /events/v1/reservation/manage/:token           správa rezervace: souhrn, formulář změny (jen zobrazí)
 * POST /events/v1/reservation/manage/:token           změna údajů, počtů, přesun na jinou přednášku
 * POST /events/v1/reservation/manage/:token/cancel    zrušení
 *
 * Token je jen v adrese (odkaz z e-mailu); do logu ani do upozornění organizátorovi se nedostane.
 */
class ReservationPublicControler extends ReservationControlerAbstract {

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            SchoolReservationService $reservations,
            private ReservationMailer $mailer) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo, $reservations);
    }

    public function book(ServerRequestInterface $request, $code): ResponseInterface {
        $offer = $this->reservations->offerByCode((string) $code);
        if ($offer === null) {
            $this->addFlashMessage('Nabídka rezervací neexistuje.', FlashSeverityEnum::WARNING);
            return $this->redirectSeeLastGet($request);
        }
        $login = $this->loginName();
        $result = $this->reservations->book($offer, $this->reservationInput($request), $login !== '' ? $login : null);
        if (!$result['ok']) {
            foreach ($result['errors'] as $error) {
                $this->addFlashMessage($error, FlashSeverityEnum::WARNING);
            }
            return $this->redirectSeeLastGet($request);
        }
        $reservation = $this->reservations->reservation($result['id']);
        $manageUrl = $this->absoluteUrl($request, 'events/v1/reservation/manage/' . $result['token']);
        $this->mail(fn() => $this->mailer->sendToPedagog(ReservationMailer::CREATED, $reservation, $manageUrl, $this->absoluteUrl($request, '')));
        $this->mail(fn() => $this->mailer->notifyOrganizer(ReservationMailer::CREATED, $reservation));
        $this->addFlashMessage('Rezervace je uložená. Potvrzení s odkazem na tuto stránku jsme poslali na ' . $reservation['contact_email'] . '.',
            FlashSeverityEnum::SUCCESS);
        return $this->createResponseRedirectSeeOther($request, 'events/v1/reservation/manage/' . $result['token'] . '?new=1');
    }

    public function manage(ServerRequestInterface $request, $token): ResponseInterface {
        $r = $this->reservations->reservationByToken((string) $token);
        if ($r === null) {
            return $this->textPage($request, 'Rezervace', 'Odkaz pro správu rezervace neplatí. Zkontrolujte, že jste ho z e-mailu zkopírovali celý, nebo kontaktujte organizátora.', 404);
        }
        if (!empty($r['anonymized_at'])) {
            return $this->textPage($request, 'Rezervace', 'Rezervace už není dostupná – údaje byly po akci smazány.', 410);
        }
        $offer = ['event_date' => $r['offer_event_date'], 'change_until' => $r['offer_change_until'], 'status' => $r['offer_status']];
        $canChange = SchoolReservationService::canOwnerChange($r, $offer, $this->reservations->now(), $this->reservations->config()->changeDaysBefore);
        return $this->page($request, 'Rezervace přednášky', 'manage.php', [
            'r' => $r,
            'token' => (string) $token,
            'canChange' => $canChange,
            'deadline' => SchoolReservationService::changeDeadline($offer, $this->reservations->config()->changeDaysBefore),
            'lectures' => $canChange ? $this->reservations->lectures((int) $r['offer_id_fk']) : [],
            'config' => $this->reservations->config(),
            'isNew' => (bool) $this->param($request, 'new', false),
            'loggedIn' => $this->loginName() !== '',
        ]);
    }

    public function manageSave(ServerRequestInterface $request, $token): ResponseInterface {
        $result = $this->reservations->updateByOwner((string) $token, $this->reservationInput($request));
        if ($result['ok']) {
            $r = $this->reservations->reservationByToken((string) $token);
            $manageUrl = $this->absoluteUrl($request, 'events/v1/reservation/manage/' . $token);
            $this->mail(fn() => $this->mailer->sendToPedagog(ReservationMailer::CHANGED, $r, $manageUrl));
            $this->mail(fn() => $this->mailer->notifyOrganizer(ReservationMailer::CHANGED, $r));
            $this->addFlashMessage(($result['moved'] ?? false) ? 'Rezervace je přesunutá na jinou přednášku. Potvrzení jsme poslali e-mailem.'
                : 'Změny jsou uložené. Potvrzení jsme poslali e-mailem.', FlashSeverityEnum::SUCCESS);
        } else {
            foreach ($result['errors'] as $error) {
                $this->addFlashMessage($error, FlashSeverityEnum::WARNING);
            }
        }
        return $this->createResponseRedirectSeeOther($request, 'events/v1/reservation/manage/' . $token);
    }

    public function manageCancel(ServerRequestInterface $request, $token): ResponseInterface {
        $r = $this->reservations->reservationByToken((string) $token);
        $result = $this->reservations->cancelByOwner((string) $token);
        if ($result['ok']) {
            $this->mail(fn() => $this->mailer->sendToPedagog(ReservationMailer::CANCELLED, $r));
            $this->mail(fn() => $this->mailer->notifyOrganizer(ReservationMailer::CANCELLED, $r));
            $this->addFlashMessage('Rezervace je zrušená. Děkujeme, že jste místa uvolnili.', FlashSeverityEnum::SUCCESS);
        } else {
            foreach ($result['errors'] as $error) {
                $this->addFlashMessage($error, FlashSeverityEnum::WARNING);
            }
        }
        return $this->createResponseRedirectSeeOther($request, 'events/v1/reservation/manage/' . $token);
    }

    /** Rezervace platí i bez e-mailu; chyba odeslání se jen zaloguje (bez tokenu a telefonu). */
    private function mail(callable $send): void {
        try {
            $send();
        } catch (\Throwable $e) {
            error_log('R1 reservation mail failed: ' . get_class($e) . ': ' . preg_replace('~manage/[A-Za-z0-9_-]+~', 'manage/***',
                $e->getMessage() . ($e->getPrevious() ? ' / ' . $e->getPrevious()->getMessage() : '')));
        }
    }
}
