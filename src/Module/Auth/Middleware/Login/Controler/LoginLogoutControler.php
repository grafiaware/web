<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Middleware\Login\Controler;
use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Request\RequestParams;
use Pes\Security\Password\Password;

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

/**
 * Description of PostControler
 *
 * @author pes2704
 */
class LoginLogoutControler extends FrontControlerAbstract {

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
                /** @var LoginAggregateFull $loginAggregateFull */
                $loginAggregateFull = $this->loginAggregateFullRepo->get($loginJmeno);

                if (isset($loginAggregateFull) AND $this->authenticator->authenticate($loginAggregateFull, $loginHeslo)) {  // z databáze
                    $securityStatus = $this->statusSecurityRepo->get();  // ze session
                    /** @var SecurityInterface $securityStatus */
                    $securityStatus->new($loginAggregateFull);
                    $this->addFlashMessage("Jste přihlášeni.", FlashSeverityEnum::SUCCESS);
                }
                else {
                    $this->addFlashMessage("Neplatné přihlášení!", FlashSeverityEnum::WARNING);
                }
            }
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
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
                    $securityStatus = $this->statusSecurityRepo->get();  // ze session
                    /** @var SecurityInterface $securityStatus */
                    $securityStatus->new($loginAggregateFull);
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
