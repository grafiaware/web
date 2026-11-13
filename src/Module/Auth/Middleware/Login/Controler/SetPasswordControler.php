<?php

namespace Auth\Middleware\Login\Controler;

use Auth\Model\Dao\CredentialsDao;
use Auth\Model\Repository\LoginAggregateFullRepo;
use Auth\Service\LoginTokenService;
use Pes\Core\Security\Password\Password;
use Pes\Http\Request\RequestParams;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Site\ConfigurationCache;
use Status\Model\Enum\FlashSeverityEnum;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

/**
 * A6: nastavení hesla odkazem z e-mailu („heslo mailem“, docs/vp/zadani/A6.md).
 *
 * GET  /auth/v1/setpassword/:token – formulář s novým heslem (token se nespotřebuje)
 * POST /auth/v1/setpassword/:token – zkontroluje heslo, spotřebuje token, uloží hash a přihlásí
 */
class SetPasswordControler extends LoginControlerAbstract {

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            private LoginTokenService $tokens,
            private LoginAggregateFullRepo $loginAggregateFullRepo) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
    }

    public function page(ServerRequestInterface $request, $token): ResponseInterface {
        $token = (string) $token;
        $loginName = $this->tokens->peek($token, LoginTokenService::PURPOSE_SET_PASSWORD);
        if ($loginName === null) {
            return $this->invalidPage($request);
        }
        $error = isset($request->getQueryParams()['chyba']) ? ConfigurationCache::auth()['passwordInfo'] : '';
        return $this->formPage($request, $token, $loginName, $error);
    }

    public function save(ServerRequestInterface $request, $token): ResponseInterface {
        $token = (string) $token;
        $auth = ConfigurationCache::auth();
        $loginName = $this->tokens->peek($token, LoginTokenService::PURPOSE_SET_PASSWORD);
        if ($loginName === null) {
            return $this->createResponseRedirectSeeOther($request, "auth/v1/setpassword/{$token}");
        }
        $password = (string) (new RequestParams())->getParsedBodyParam($request, $auth['fieldNameHeslo'], '');
        if (!preg_match('/^(?:' . $auth['passwordPattern'] . ')$/u', $password)) {
            // heslo neodpovídá pravidlu – token zůstává platný, zpět na formulář s chybou
            return $this->createResponseRedirectSeeOther($request, "auth/v1/setpassword/{$token}?chyba=1");
        }
        if ($this->tokens->consume($token, LoginTokenService::PURPOSE_SET_PASSWORD) !== $loginName) {
            return $this->createResponseRedirectSeeOther($request, "auth/v1/setpassword/{$token}");
        }
        /** @var CredentialsDao $credentialsDao */
        $credentialsDao = $this->container->get(CredentialsDao::class);
        $credentials = $credentialsDao->get(['login_name_fk' => $loginName]);
        if ($credentials === null) {
            return $this->createResponseRedirectSeeOther($request, "auth/v1/setpassword/{$token}");
        }
        $credentials['password_hash'] = (new Password())->getPasswordHash($password);
        $credentialsDao->update($credentials);

        $loginAggregateFull = $this->loginAggregateFullRepo->get($loginName);
        if (isset($loginAggregateFull)) {
            $this->startUserContext($loginAggregateFull);
            $this->addFlashMessage("Heslo je nastavené, jste přihlášeni. Příště se přihlásíte jménem {$loginName} a heslem.", FlashSeverityEnum::SUCCESS);
        } else {
            $this->addFlashMessage("Heslo je nastavené. Přihlaste se jménem {$loginName}.", FlashSeverityEnum::SUCCESS);
        }
        return $this->createResponseRedirectSeeOther($request, '');
    }

    private function formPage(ServerRequestInterface $request, string $token, string $loginName, string $error): ResponseInterface {
        $auth = ConfigurationCache::auth();
        return $this->createActionPageResponse($request, 'Nastavení hesla',
            "Nastavte si heslo k účtu {$loginName}. Odkaz lze použít jen jednou.",
            "auth/v1/setpassword/{$token}", 'Nastavit heslo',
            ['name' => $auth['fieldNameHeslo'], 'pattern' => $auth['passwordPattern'], 'info' => $auth['passwordInfo'], 'error' => $error]);
    }

    private function invalidPage(ServerRequestInterface $request): ResponseInterface {
        return $this->createActionPageResponse($request, 'Odkaz už neplatí',
            'Odkaz na nastavení hesla už byl použit nebo vypršela jeho platnost. Požádejte administrátora o nový.');
    }
}
