<?php

namespace Events\Middleware\Events\Controler\Reservation;

use Access\Enum\RoleEnum;
use Events\Service\Reservation\SchoolReservationService;
use FrontControler\FrontControlerAbstract;
use Pes\View\Renderer\PhpTemplateRenderer;
use Pes\View\ViewFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

/**
 * R1 (#48): společný základ kontrolérů rezervací pro školy – role správce, samostatné stránky Pages/ (ADR 002),
 * úklid telefonů po akci při každém požadavku (dokud není plánovač Z2).
 */
abstract class ReservationControlerAbstract extends FrontControlerAbstract {

    /** Role správce rezervací (zadání R1: events_administrator). */
    public const ADMIN_ROLES = [RoleEnum::EVENTS_ADMINISTRATOR];

    /** Složka stránek; pole formuláře Pages/_fields.php používá i statická šablona vp/rezervace-skol. */
    public const PAGES_DIR = __DIR__ . '/Pages/';

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            protected SchoolReservationService $reservations) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        try {
            $this->reservations->housekeeping();
        } catch (\Throwable $e) {
            error_log('R1 reservation housekeeping failed: ' . $e->getMessage());
        }
    }

    protected function role(): ?string {
        $loginAggregate = $this->statusSecurityRepo->getClone()->getLoginAggregate();
        return isset($loginAggregate) ? $loginAggregate->getCredentials()?->getRoleFk() : null;
    }

    protected function isAdmin(): bool {
        return in_array($this->role(), self::ADMIN_ROLES, true);
    }

    protected function loginName(): string {
        return (string) $this->getLoginUserName();
    }

    /** Samostatná stránka bez layoutu webu. $template je soubor v Pages/. */
    protected function page(ServerRequestInterface $request, string $title, string $template, array $data = [], int $status = 200): ResponseInterface {
        $base = $this->getBasePath($request);
        $render = fn(string $file, array $vars) => (string) (new ViewFactory())->phpTemplateView(__DIR__ . '/Pages/' . $file, $vars)
            ->setRenderer(new PhpTemplateRenderer());
        $body = $render($template, $data + ['base' => $base]);
        $html = $render('_layout.php', [
            'title' => $title,
            'body' => $body,
            'base' => $base,
            'messages' => $this->flashMessages(),
            'isAdmin' => $this->isAdmin(),
        ]);
        return $this->createStringOKResponse($html, $status)->withHeader('Cache-Control', 'no-store');
    }

    protected function textPage(ServerRequestInterface $request, string $title, string $text, int $status = 200): ResponseInterface {
        return $this->page($request, $title, 'text.php', ['text' => $text], $status);
    }

    protected function flashMessages(): array {
        try {
            return $this->statusFlashRepo->get()->getMessages();
        } catch (\Throwable $e) {
            return [];
        }
    }

    protected function param(ServerRequestInterface $request, string $name, $default = null) {
        $body = $request->getParsedBody();
        if (is_array($body) && array_key_exists($name, $body)) {
            return $body[$name];
        }
        return $request->getQueryParams()[$name] ?? $default;
    }

    /** Vstup formuláře rezervace (stejná pole ve formuláři webu, ve správě odkazem i v administraci). */
    protected function reservationInput(ServerRequestInterface $request): array {
        $in = [];
        foreach (['event_id', 'school_name', 'school_place', 'field_code', 'school_class', 'pupils', 'teachers', 'contact_email',
                'contact_name', 'contact_phone', 'phone_consent', 'note', 'consent', \Events\Service\Reservation\ReservationValidator::HONEYPOT] as $k) {
            $value = $this->param($request, $k);
            $in[$k] = is_array($value) ? '' : $value;
        }
        return $in;
    }
}
