<?php

namespace Auth\Middleware\Login\Controler;

use Auth\Service\AccountException;
use Auth\Service\AccountRequest;
use Auth\Service\AccountService;
use Pes\Http\Request\RequestParams;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Site\ConfigurationCache;
use Status\Model\Enum\FlashSeverityEnum;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

/**
 * A6: zakládání účtů administrátorem (docs/vp/zadani/A6.md).
 *
 * POST /auth/v1/account                              – založí účet z formuláře v admin-credentials (flash + přesměrování)
 * POST /auth/v1/api/account                          – totéž jako API pro jinou aplikaci (JSON 201 / 400 / 403)
 * POST /auth/v1/account/:loginname/setpasswordlink   – pošle nový odkaz na nastavení hesla (flash + přesměrování)
 */
class AccountControler extends LoginControlerAbstract {

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            private AccountService $accountService) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
    }

    public function create(ServerRequestInterface $request): ResponseInterface {
        return $this->handleCreate($request, false);
    }

    public function createJson(ServerRequestInterface $request): ResponseInterface {
        return $this->handleCreate($request, true);
    }

    private function handleCreate(ServerRequestInterface $request, bool $json): ResponseInterface {
        $params = new RequestParams();
        $p = fn(string $name) => (string) $params->getParsedBodyParam($request, $name, '');
        $ttlHours = $this->setPasswordTtlHours();
        try {
            $result = $this->accountService->create(new AccountRequest(
                email: $p('email'),
                role: $p('role'),
                loginName: $p('loginName'),
                password: $p('password'),
                passwordByMail: (bool) $params->getParsedBodyParam($request, 'passwordByMail', false),
                displayName: $this->featureFlags()->isEnabled('vp.osobni-jmeno') ? $p('displayName') : null,
                setPasswordTtlHours: $ttlHours,
            ), $this->currentRole());
        } catch (AccountException $e) {
            return $this->failure($request, $e, $json);
        }

        if ($result->setPasswordToken !== null) {
            $this->authMailer()->sendSetPasswordLink($result->email, $result->loginName,
                $this->absoluteUrl($request, "auth/v1/setpassword/{$result->setPasswordToken}"), $ttlHours);
        }
        $message = "Účet {$result->loginName} (role {$result->role}) je založený."
            . ($result->setPasswordToken !== null ? " Na {$result->email} jsme poslali odkaz na nastavení hesla." : '')
            . ($result->role === 'representative' ? ' K firmě ho přiřadíte v administraci firem (zástupci firmy).' : '');
        if ($json) {
            return $this->createJsonPostCreatedResponse([
                'loginName' => $result->loginName,
                'email' => $result->email,
                'role' => $result->role,
                'displayName' => $result->displayName,
                'setPasswordLinkSent' => $result->setPasswordToken !== null,
                'warning' => $result->eventsWarning,
            ]);
        }
        $this->addFlashMessage($message, FlashSeverityEnum::SUCCESS);
        if ($result->eventsWarning !== null) {
            $this->addFlashMessage($result->eventsWarning, FlashSeverityEnum::WARNING);
        }
        return $this->redirectSeeLastGet($request);
    }

    public function sendSetPasswordLink(ServerRequestInterface $request, $loginName): ResponseInterface {
        $loginName = (string) $loginName;
        $ttlHours = $this->setPasswordTtlHours();
        try {
            $link = $this->accountService->newSetPasswordLink($loginName, $this->currentRole(), $ttlHours);
        } catch (AccountException $e) {
            return $this->failure($request, $e, false);
        }
        $this->authMailer()->sendSetPasswordLink($link['email'], $loginName,
            $this->absoluteUrl($request, "auth/v1/setpassword/{$link['token']}"), $ttlHours);
        $this->addFlashMessage("Odkaz na nastavení hesla pro účet {$loginName} jsme poslali na {$link['email']}.", FlashSeverityEnum::SUCCESS);
        return $this->redirectSeeLastGet($request);
    }

    private function failure(ServerRequestInterface $request, AccountException $e, bool $json): ResponseInterface {
        if ($json) {
            return $this->createJsonOKResponse(['error' => $e->getMessage()], $e->httpStatus);
        }
        if ($this->currentRole() === null) {
            return $this->createUnauthorizedResponse();
        }
        $this->addFlashMessage($e->getMessage(), FlashSeverityEnum::WARNING);
        return $this->redirectSeeLastGet($request);
    }

    private function currentRole(): ?string {
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        return $loginAggregate?->getCredentials()?->getRoleFk();
    }

    private function setPasswordTtlHours(): int {
        return (int) (ConfigurationCache::auth()['setPasswordLinkTtlHours'] ?? 72);
    }
}
