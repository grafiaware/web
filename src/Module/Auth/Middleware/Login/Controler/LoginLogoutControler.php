<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Middleware\Login\Controler;
use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Request\RequestParams;
use Pes\Core\Security\Password\Password;

use FrontControler\FrontControlerAbstract;

use Site\ConfigurationCache;

use Auth\Authenticator\AuthenticatorInterface;

// model
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Auth\Model\Repository\LoginAggregateFullRepo;

use Auth\Model\Entity\LoginAggregateFull;

use Status\Model\Entity\SecurityInterface;
use Status\Model\Enum\FlashSeverityEnum;
use Auth\Model\Dao\RegistrationDao;
use Auth\Service\GlobalRoleGrantor;
use Auth\Service\LoginLog;
use Auth\Service\LoginThrottle;
use Auth\Service\RoleGrantPolicy;

/**
 * Description of PostControler
 *
 * @author pes2704
 */
class LoginLogoutControler extends LoginControlerAbstract {

    /** Hash pro ověření hesla u neexistujícího účtu (stejná doba odezvy) – heslo k němu neexistuje. */
    private const DUMMY_HASH = '$2y$12$2IOAejUY1CucOFU9wpAoE.2scljtOHfNqg7JSazOeJeaBgePLcI0C';

    private $authenticator;
    private $superAuthenticator;

    private $loginAggregateFullRepo;

    /**
     *
     */
    public function __construct(
                        StatusSecurityRepo $statusSecurityRepo,
                           StatusFlashRepo $statusFlashRepo,
                    StatusPresentationRepo $statusPresentationRepo,
            LoginAggregateFullRepo $loginAggregateFullRepo,
                    AuthenticatorInterface $authenticator,
                    AuthenticatorInterface $superAuthenticator) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->loginAggregateFullRepo = $loginAggregateFullRepo;
        $this->authenticator = $authenticator;
        $this->superAuthenticator = $superAuthenticator;
    }

    public function login(ServerRequestInterface $request) {
        $requestParams = new RequestParams();
        $login = $requestParams->getParsedBodyParam($request, 'login', FALSE);

        if ($login) {
            // používá názvy z konfigurace pro omezení množství našeptávaných jmen při vypl%nování formuláře v prohlížečích
            $fieldNameJmeno = ConfigurationCache::auth()['fieldNameJmeno'];
            $fieldNameHeslo = ConfigurationCache::auth()['fieldNameHeslo'];
            $loginJmeno = $requestParams->getParsedBodyParam($request, $fieldNameJmeno, FALSE);
            $loginHeslo = $requestParams->getParsedBodyParam($request, $fieldNameHeslo, FALSE);

            if ($loginJmeno AND $loginHeslo) {
                if ($this->featureFlags()->isEnabled('vp.registrace-emailem') AND str_contains($loginJmeno, '@')) {
                    // A1: přihlášení e-mailem – jen pokud e-mail patří jedinému potvrzenému účtu
                    $loginNames = $this->container->get(RegistrationDao::class)->findLoginNamesByEmail($loginJmeno, true);
                    if (count($loginNames) > 1) {
                        $this->addFlashMessage("K tomuto e-mailu patří více účtů. Přihlaste se přihlašovacím jménem.", FlashSeverityEnum::WARNING);
                        return $this->redirectSeeLastGet($request); // 303 See Other
                    }
                    $loginJmeno = $loginNames[0] ?? '';
                }
                if ($this->featureFlags()->isEnabled(RoleGrantPolicy::FEATURE)) {
                    return $this->loginA9($request, (string) $loginJmeno, (string) $loginHeslo);
                }
                /** @var LoginAggregateFull $loginAggregateFull */
                $loginAggregateFull = $loginJmeno ? $this->loginAggregateFullRepo->get($loginJmeno) : null;

                if (isset($loginAggregateFull) AND $this->authenticator->authenticate($loginAggregateFull, $loginHeslo)) {  // z databáze
                    $this->startUserContext($loginAggregateFull);  // session + osobní jméno (A8)
                    $this->addFlashMessage("Jste přihlášeni.", FlashSeverityEnum::SUCCESS);
                }
                else {
                    $this->addFlashMessage("Neplatné přihlášení!", FlashSeverityEnum::WARNING);
                }
            }
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
    
    /**
     * A9 (docs/vp/zadani/A9.md): přihlášení heslem s účtem global_role_grantor z konfigurace, zpomalením a logy.
     */
    private function loginA9(ServerRequestInterface $request, string $loginJmeno, string $loginHeslo) {
        /** @var LoginLog $log */
        $log = $this->container->get(LoginLog::class);
        /** @var GlobalRoleGrantor $grantor */
        $grantor = $this->container->get(GlobalRoleGrantor::class);
        if ($loginJmeno !== '' && $grantor->matches($loginJmeno)) {
            // účet z konfigurace má přednost před stejnojmenným účtem v DB; bez zpomalení (místo něj varování na webu)
            if ($grantor->verify($loginHeslo)) {
                $this->startUserContext($grantor->loginAggregate(), 'heslo', true);
                $this->addFlashMessage("Jste přihlášeni jako správce přidělování rolí z konfigurace serveru.", FlashSeverityEnum::SUCCESS);
            } else {
                $log->failure($grantor->loginName(), null, 'špatné heslo');
                $this->addFlashMessage("Neplatné přihlášení!", FlashSeverityEnum::WARNING);
            }
            return $this->redirectSeeLastGet($request);
        }

        /** @var LoginAggregateFull $loginAggregateFull */
        $loginAggregateFull = $loginJmeno !== '' ? $this->loginAggregateFullRepo->get($loginJmeno) : null;
        if (!isset($loginAggregateFull)) {
            password_verify($loginHeslo, self::DUMMY_HASH);   // stejná doba odezvy jako u existujícího účtu
            $log->failure(null, null, 'účet neexistuje');
            $this->addFlashMessage("Neplatné přihlášení!", FlashSeverityEnum::WARNING);
            return $this->redirectSeeLastGet($request);
        }
        $loginName = $loginAggregateFull->getLoginName();
        $origin = $this->accountOrigin($loginName);
        /** @var LoginThrottle $throttle */
        $throttle = $this->container->get(LoginThrottle::class);
        $locked = $throttle->secondsLocked($loginName);
        if ($locked > 0) {
            $log->failure($loginName, $origin, 'blokováno – příliš mnoho pokusů');
            $this->addFlashMessage("Příliš mnoho neúspěšných pokusů. Zkuste to znovu " . LoginThrottle::waitText($locked) . ".", FlashSeverityEnum::WARNING);
            return $this->redirectSeeLastGet($request);
        }
        if ($this->authenticator->authenticate($loginAggregateFull, $loginHeslo)) {
            $this->startUserContext($loginAggregateFull, 'heslo');   // nuluje zpomalení, zapíše log a poslední přihlášení
            $this->addFlashMessage("Jste přihlášeni.", FlashSeverityEnum::SUCCESS);
            return $this->redirectSeeLastGet($request);
        }
        $delay = $throttle->failure($loginName);
        $log->failure($loginName, $origin, 'špatné heslo');
        $this->addFlashMessage($delay > 0
                ? "Neplatné přihlášení! Příliš mnoho neúspěšných pokusů – další pokus " . LoginThrottle::waitText($delay) . "."
                : "Neplatné přihlášení!", FlashSeverityEnum::WARNING);
        return $this->redirectSeeLastGet($request);
    }

    public function superlogin(ServerRequestInterface $request) {
        $requestParams = new RequestParams();
        $login = $requestParams->getParsedBodyParam($request, 'login', FALSE);

        if ($login) {
            // používá názvy z konfigurace pro omezení množství našeptávaných jmen při vypl%nování formuláře v prohlížečích
            $fieldNameJmeno = ConfigurationCache::auth()['fieldNameJmeno'];
            $fieldNameHeslo = ConfigurationCache::auth()['fieldNameHeslo'];
            $loginJmeno = $requestParams->getParsedBodyParam($request, $fieldNameJmeno, FALSE);
            $loginHeslo = $requestParams->getParsedBodyParam($request, $fieldNameHeslo, FALSE);

            if ($loginJmeno AND $loginHeslo) {
                /** @var LoginAggregateFull $loginAggregateFull */
                $loginAggregateFull = $this->loginAggregateFullRepo->get($loginJmeno);

                if (isset($loginAggregateFull) AND $this->superAuthenticator->authenticate($loginAggregateFull, $loginHeslo)) {  // z databáze
                    $this->startUserContext($loginAggregateFull);  // session + osobní jméno (A8)
                    $this->addFlashMessage("Jste přihlášeni.", FlashSeverityEnum::SUCCESS);
                }
                else {
                    $this->addFlashMessage("Neplatné přihlášení!", FlashSeverityEnum::WARNING);
                }
            }
        }
        return $this->redirectSeeLastGet($request); // 303 See Other        
    }

    public function logout(ServerRequestInterface $request) {
        $logout = (new RequestParams())->getParsedBodyParam($request, 'logout', FALSE);
        if ($logout) {
            $this->statusSecurityRepo->get()->removeContext();
            $this->addFlashMessage("Jste odhlášeni.");
        }
        return $this->redirectSeeLastGet($request); // 303 See Other

    }

}
