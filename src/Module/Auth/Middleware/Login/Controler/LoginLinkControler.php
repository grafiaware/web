<?php

namespace Auth\Middleware\Login\Controler;

use Auth\Model\Dao\RegistrationDao;
use Auth\Model\Repository\LoginAggregateFullRepo;
use Auth\Service\LoginTokenService;
use Pes\Http\Request\RequestParams;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Status\Model\Enum\FlashSeverityEnum;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

/**
 * A2: přihlašovací odkaz e-mailem (docs/vp/zadani/A1.md).
 *
 * POST /auth/v1/loginlink          – pošle odkaz(y) na e-mail, odpověď je vždy stejná
 * GET  /auth/v1/loginlink/:token   – stránka s tlačítkem (GET token nespotřebuje)
 * POST /auth/v1/loginlink/:token   – spotřebuje token a přihlásí
 */
class LoginLinkControler extends LoginControlerAbstract {

    public const INFO_PASSWORD_SET_UNTIL = 'passwordSetAllowedUntil';

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            private LoginTokenService $tokens,
            private LoginAggregateFullRepo $loginAggregateFullRepo) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
    }

    public function send(ServerRequestInterface $request): ResponseInterface {
        $email = trim((string) (new RequestParams())->getParsedBodyParam($request, 'email', ''));
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->addFlashMessage("Zadejte platnou e-mailovou adresu.", FlashSeverityEnum::WARNING);
            return $this->redirectSeeLastGet($request);
        }
        $loginNames = $this->container->get(RegistrationDao::class)->findLoginNamesByEmail($email, true);
        if ($loginNames) {
            $this->sendLoginLinks($request, $email, $loginNames);
        }
        $this->addFlashMessage("Pokud u nás účet s adresou {$email} je, poslali jsme na ni přihlašovací odkaz. Platí "
                . ((int) (\Site\ConfigurationCache::auth()['loginLinkTtlMinutes'] ?? 30)) . " minut.");
        return $this->redirectSeeLastGet($request);
    }

    public function page(ServerRequestInterface $request, $token): ResponseInterface {
        $token = (string) $token;
        if ($this->tokens->peek($token, LoginTokenService::PURPOSE_LOGIN) === null) {
            return $this->invalidPage($request);
        }
        return $this->createActionPageResponse($request, 'Přihlášení',
            'Přihlaste se tlačítkem. Odkaz lze použít jen jednou.', "auth/v1/loginlink/{$token}", 'Přihlásit se');
    }

    public function login(ServerRequestInterface $request, $token): ResponseInterface {
        $loginName = $this->tokens->consume((string) $token, LoginTokenService::PURPOSE_LOGIN);
        $loginAggregateFull = $loginName !== null ? $this->loginAggregateFullRepo->get($loginName) : null;
        if (!isset($loginAggregateFull) || $loginAggregateFull->getCredentials() === null) {
            $this->addFlashMessage("Přihlašovací odkaz už není platný. Nechte si poslat nový.", FlashSeverityEnum::WARNING);
            return $this->createResponseRedirectSeeOther($request, '');
        }
        $security = $this->statusSecurityRepo->get();
        $security->newContext($loginAggregateFull);
        // po přihlášení odkazem lze nastavit nové heslo bez starého (po dobu platnosti odkazu)
        $ttl = (int) (\Site\ConfigurationCache::auth()['loginLinkTtlMinutes'] ?? 30);
        $security->setInfo(self::INFO_PASSWORD_SET_UNTIL, time() + 60 * $ttl);
        $this->addFlashMessage("Jste přihlášeni. Nové heslo si můžete nastavit v nabídce účtu („Chci si změnit heslo“).", FlashSeverityEnum::SUCCESS);
        return $this->createResponseRedirectSeeOther($request, '');
    }

    private function invalidPage(ServerRequestInterface $request): ResponseInterface {
        return $this->createActionPageResponse($request, 'Odkaz už neplatí',
            'Přihlašovací odkaz už byl použit nebo vypršela jeho platnost. Na webu si nechte poslat nový („Poslat přihlašovací odkaz“).');
    }
}
