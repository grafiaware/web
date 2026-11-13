<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Middleware\Login\Controler;

// controller
use FrontControler\FrontControlerAbstract;
use Application\Feature\FeatureFlags;
use Auth\Service\AuthMailer;
use Auth\Service\LoginTokenService;
use Auth\Service\DisplayNameService;
use Auth\Service\AccountOrigin;
use Auth\Service\LoginLog;
use Auth\Service\LoginThrottle;
use Auth\Service\RoleGrantPolicy;
use Auth\Model\Dao\LoginProfileDao;
use Auth\Model\Dao\RegistrationDao;
use Auth\Model\Entity\LoginAggregateFullInterface;
use Status\Model\Entity\SecurityInterface;
use Site\ConfigurationCache;
use Mail\MailSettings;
use Mail\MessageFactory\HtmlMessage;
use Pes\Mail\MailInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// view a template
use \Pes\View\ViewFactory;
use \Pes\View\Renderer\PhpTemplateRenderer;

/**
 * Description of LoginControlerAbstract
 *
 * @author pes2704
 */
class LoginControlerAbstract extends FrontControlerAbstract {

    private ?FeatureFlags $featureFlags = null;

    /**
     * Přepínače funkcí – výchozí z konfigurace aktivní site; v testech lze podstrčit.
     */
    public function setFeatureFlags(FeatureFlags $featureFlags): static {
        $this->featureFlags = $featureFlags;
        return $this;
    }

    protected function featureFlags(): FeatureFlags {
        return $this->featureFlags ??= FeatureFlags::fromConfiguration();
    }

    /**
     * Absolutní adresa v rámci aplikace (odkazy v e-mailech), $restUri bez úvodního lomítka.
     */
    protected function absoluteUrl(ServerRequestInterface $request, string $restUri): string {
        $scheme = $request->getUri()->getScheme() ?: 'https';
        $host = $request->getServerParams()['HTTP_HOST'] ?? $request->getUri()->getHost();
        return $scheme . '://' . $host . $this->getBasePath($request) . $restUri;  // getBasePath končí /
    }

    /**
     * Přihlásí uživatele (nový bezpečnostní kontext v session) a načte jeho osobní jméno (A8) pro zobrazení.
     * Všechna přihlášení v Auth jdou přes tuto metodu.
     */
    protected function startUserContext(LoginAggregateFullInterface $loginAggregateFull, string $method = 'heslo',
            bool $fromConfiguration = false): SecurityInterface {
        $security = $this->statusSecurityRepo->get();
        $security->newContext($loginAggregateFull);
        if ($this->featureFlags()->isEnabled(RoleGrantPolicy::FEATURE)) {
            // A9: čas posledního přihlášení, vynulování zpomalení, log přihlášení
            $loginName = $loginAggregateFull->getLoginName();
            try {
                if (!$fromConfiguration) {
                    $this->container->get(LoginThrottle::class)->success($loginName);
                }
                $this->container->get(LoginLog::class)->success($loginName, $fromConfiguration ? null : $this->accountOrigin($loginName), $method);
            } catch (\Throwable $e) {
                // log a statistika nesmí zabránit přihlášení
            }
        }
        $displayName = null;
        if ($this->featureFlags()->isEnabled('vp.osobni-jmeno')) {
            $displayName = $this->container->get(DisplayNameService::class)->get($loginAggregateFull->getLoginName());
        }
        $security->setInfo(SecurityInterface::INFO_DISPLAY_NAME, $displayName);
        return $security;
    }

    /**
     * A9: typ účtu (self | admin | legacy) – z login_profile.origin, jinak odvozený z registrace.
     */
    protected function accountOrigin(string $loginName): string {
        $stored = $this->container->get(LoginProfileDao::class)->getOrigin($loginName);
        $registration = $stored === null ? $this->container->get(RegistrationDao::class)->get(['login_name_fk' => $loginName]) : null;
        return AccountOrigin::resolve($stored, $registration !== null ? (string) $registration['email'] : null);
    }

    protected function authMailer(): AuthMailer {
        return new AuthMailer(
            $this->container->get(MailInterface::class),
            $this->container->get(HtmlMessage::class),
            MailSettings::fromConfiguration());
    }

    /**
     * Jednoduchá samostatná stránka s jedním tlačítkem (potvrzení registrace, přihlášení odkazem).
     * Akce se provádí až POSTem – GET odkaz z e-mailu může otevřít i bezpečnostní skener pošty.
     */
    protected function createActionPageResponse(ServerRequestInterface $request, string $title, string $text,
            string $formRestUri = '', string $buttonText = '', array $passwordField = []): ResponseInterface {
        $html = (string) (new ViewFactory())->phpTemplateView(__DIR__ . '/Pages/action.php', [
                'title' => $title,
                'text' => $text,
                'formAction' => $formRestUri === '' ? '' : $this->getBasePath($request) . $formRestUri,
                'buttonText' => $buttonText,
                'homeUrl' => $this->getBasePath($request),
                // A6: volitelné pole pro nové heslo ['name', 'pattern', 'info', 'error']
                'passwordField' => $passwordField,
            ])->setRenderer(new PhpTemplateRenderer());
        return $this->createStringOKResponse($html)->withHeader('Cache-Control', 'no-store');
    }

    /**
     * Pošle jeden e-mail s přihlašovacím odkazem pro každý z loginů (nad limit odkazů se login vynechá).
     *
     * @param string[] $loginNames
     */
    protected function sendLoginLinks(ServerRequestInterface $request, string $email, array $loginNames): void
    {
        $auth = ConfigurationCache::auth();
        $ttl = (int) ($auth['loginLinkTtlMinutes'] ?? 30);
        /** @var LoginTokenService $tokens */
        $tokens = $this->container->get(LoginTokenService::class);
        $links = [];
        foreach ($loginNames as $loginName) {
            if ($tokens->canIssue($loginName, LoginTokenService::PURPOSE_LOGIN, (int) ($auth['loginLinkLimitCount'] ?? 3), (int) ($auth['loginLinkLimitMinutes'] ?? 15))) {
                $token = $tokens->issue($loginName, LoginTokenService::PURPOSE_LOGIN, $ttl);
                $links[] = ['loginName' => $loginName, 'url' => $this->absoluteUrl($request, "auth/v1/loginlink/{$token}")];
            }
        }
        if ($links) {
            $this->authMailer()->sendLoginLinks($email, $links, $ttl);
        }
    }
}
