<?php

namespace Events\Middleware\Events\Controler\Lottery;

use Access\Enum\RoleEnum;
use Events\Service\Lottery\LotteryService;
use FrontControler\FrontControlerAbstract;
use Pes\View\Renderer\PhpTemplateRenderer;
use Pes\View\ViewFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

/**
 * D1 (#46): společný základ kontrolérů losování – role, samostatné stránky pro telefon, absolutní adresy.
 *
 * Oprávnění obsluhy (vstup, infostánek, losování) se kontroluje jen v isStaff(); užší role „obsluha losování“
 * z #90 se doplní sem.
 */
abstract class LotteryControlerAbstract extends FrontControlerAbstract {

    /** Role, které smí obsluhovat losování (skener, losování kola, převzetí). */
    public const STAFF_ROLES = [RoleEnum::EVENTS_ADMINISTRATOR];
    /** Role, které smí losování nastavovat (losování, kola, ceny, anketa, export). */
    public const ADMIN_ROLES = [RoleEnum::EVENTS_ADMINISTRATOR];

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            protected LotteryService $lotteries) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
    }

    protected function role(): ?string {
        $loginAggregate = $this->statusSecurityRepo->getClone()->getLoginAggregate();
        return isset($loginAggregate) ? $loginAggregate->getCredentials()?->getRoleFk() : null;
    }

    protected function loginName(): string {
        return (string) $this->getLoginUserName();
    }

    protected function isStaff(): bool {
        return in_array($this->role(), self::STAFF_ROLES, true);
    }

    protected function isAdmin(): bool {
        return in_array($this->role(), self::ADMIN_ROLES, true);
    }

    protected function isVisitor(): bool {
        return $this->role() === RoleEnum::VISITOR;
    }

    /**
     * Samostatná stránka (bez layoutu webu, pro telefon). $template je soubor v Pages/.
     */
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
            'isStaff' => $this->isStaff(),
            'isAdmin' => $this->isAdmin(),
            'head' => $data['head'] ?? '',
        ]);
        return $this->createStringOKResponse($html, $status)->withHeader('Cache-Control', 'no-store');
    }

    protected function deniedPage(ServerRequestInterface $request, string $text = 'Tato stránka je jen pro obsluhu losování. Přihlaste se účtem obsluhy.'): ResponseInterface {
        return $this->page($request, 'Přístup odepřen', 'text.php', ['text' => $text], 403);
    }

    /** Zprávy z předchozího požadavku (POST → přesměrování). */
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
}
