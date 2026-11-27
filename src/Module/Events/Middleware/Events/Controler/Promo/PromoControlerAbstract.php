<?php

namespace Events\Middleware\Events\Controler\Promo;

use Access\Enum\RoleEnum;
use Events\Service\Promo\PromoLinkService;
use FrontControler\FrontControlerAbstract;
use Pes\View\Renderer\PhpTemplateRenderer;
use Pes\View\ViewFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

/**
 * F2 (#47): společný základ kontrolérů odkazů s měřením – role media_manager, samostatné stránky Pages/ (ADR 002).
 */
abstract class PromoControlerAbstract extends FrontControlerAbstract {

    /** Role správce odkazů a přehledu (zadání F1: jen media_manager; kdo potřebuje i jinou roli, má druhý účet). */
    public const ADMIN_ROLES = [RoleEnum::MEDIA_MANAGER];

    /** Složka stránek; blok podkladů ke zkopírování (_copy.php) používá i promo kit v profilu vystavovatele. */
    public const PAGES_DIR = __DIR__ . '/Pages/';

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            protected PromoLinkService $links) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
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

    /** Kořen aplikace v URL ('/' nebo '/web/'). */
    protected function rootPath(ServerRequestInterface $request): string {
        return $this->getUriInfo($request)->getRootAbsolutePath();
    }

    /** Samostatná stránka bez layoutu webu. $template je soubor v Pages/. */
    protected function page(ServerRequestInterface $request, string $title, string $template, array $data = [], int $status = 200): ResponseInterface {
        $base = $this->getBasePath($request);
        $render = fn(string $file, array $vars) => (string) (new ViewFactory())->phpTemplateView(self::PAGES_DIR . $file, $vars)
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
}
