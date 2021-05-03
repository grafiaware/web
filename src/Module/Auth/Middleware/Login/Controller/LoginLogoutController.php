<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Module\Auth\Middleware\Login\Controller;

use Site\Configuration;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Request\RequestParams;
use Security\Auth\AuthenticatorInterface;
use Pes\Security\Password\Password;

// model
use Module\Status\Model\Repository\StatusPresentationRepo;
use Module\Status\Model\Repository\StatusSecurityRepo;
use Module\Status\Model\Repository\StatusFlashRepo;
use Model\Repository\CredentialsRepo;
use Model\Repository\LoginAggregateFullRepo;

use Model\Entity\Credentials;
use Model\Entity\LoginAggregateFull;

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
            ResourceRegistryInterface $resourceRegistry=null,
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
            $fieldNameJmeno = Configuration::loginLogoutController()['fieldNameJmeno'];
            $fieldNameHeslo = Configuration::loginLogoutController()['fieldNameHeslo'];
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
