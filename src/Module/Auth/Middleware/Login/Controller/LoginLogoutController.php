<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Middleware\Login\Controller;

use Site\ConfigurationCache;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Request\RequestParams;
use Pes\Security\Password\Password;

use Auth\Authenticator\AuthenticatorInterface;

// model
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Auth\Model\Repository\CredentialsRepo;
use Auth\Model\Repository\LoginAggregateFullRepo;

use Auth\Model\Entity\Credentials;
use Auth\Model\Entity\LoginAggregateFull;

/**
 * Description of PostController
 *
 * @author pes2704
 */
class LoginLogoutController extends LoginControllerAbstract {

    private $authenticator;

    private $loginAggregateFullRepo;

    /**
     *
     */
    public function __construct(
                        StatusSecurityRepo $statusSecurityRepo,
                           StatusFlashRepo $statusFlashRepo,
                    StatusPresentationRepo $statusPresentationRepo,
            LoginAggregateFullRepo $loginAggregateFullRepo,
                    AuthenticatorInterface $authenticator) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->loginAggregateFullRepo = $loginAggregateFullRepo;
        $this->authenticator = $authenticator;
    }

    public function login(ServerRequestInterface $request) {
        $requestParams = new RequestParams();
        $login = $requestParams->getParsedBodyParam($request, 'login', FALSE);

        if ($login) {
            // používá názvy z konfigurace pro omezení množství našeptávaných jmen při vypl%nování formuláře v prohlížečích
            $fieldNameJmeno = ConfigurationCache::loginLogoutController()['fieldNameJmeno'];
            $fieldNameHeslo = ConfigurationCache::loginLogoutController()['fieldNameHeslo'];
            $loginJmeno = $requestParams->getParsedBodyParam($request, $fieldNameJmeno, FALSE);
            $loginHeslo = $requestParams->getParsedBodyParam($request, $fieldNameHeslo, FALSE);

            if ($loginJmeno AND $loginHeslo) {
                /** @var LoginAggregateFull $loginAggregateFull */
                $loginAggregateFull = $this->loginAggregateFullRepo->get($loginJmeno);

                    if (isset($loginAggregateFull) AND $this->authenticator->authenticate($loginAggregateFull, $loginHeslo)) {  // z databáze
                        $this->statusSecurityRepo->get()->renewSecurityStatus($loginAggregateFull);
                        $this->addFlashMessage("Jste přihlášeni.");
                    }
                    else {
                        $this->addFlashMessage("Neplatné přihlášení!");
                    }
            }
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function logout(ServerRequestInterface $request) {
        $logout = (new RequestParams())->getParsedBodyParam($request, 'logout', FALSE);
        if ($logout) {
            $this->statusSecurityRepo->get()->renewSecurityStatus(null);  // bez parametru loginAggregateEntity
            $this->addFlashMessage("Jste odhlášeni.");
        }
        return $this->redirectSeeLastGet($request); // 303 See Other

    }

}