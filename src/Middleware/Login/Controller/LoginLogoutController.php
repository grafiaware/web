<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Login\Controller;

use Site\Configuration;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Request\RequestParams;
use Security\Auth\AuthenticatorInterface;
use Pes\Security\Password\Password;

// controller
use Controller\StatusFrontControllerAbstract;

// model
use Model\Repository\StatusPresentationRepo;
use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusFlashRepo;
use Model\Repository\CredentialsRepo;
use Model\Repository\LoginAggregateRepo;

use Model\Entity\Credentials;
use Model\Entity\LoginAggregate;

/**
 * Description of PostController
 *
 * @author pes2704
 */
class LoginLogoutController extends StatusFrontControllerAbstract {

    private $authenticator;

    private $loginAggregateRepo;

    /**
     *
     */
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            LoginAggregateRepo $loginAggregateRepo,
            AuthenticatorInterface $authenticator) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->loginAggregateRepo = $loginAggregateRepo;
        $this->authenticator = $authenticator;
    }

    public function login(ServerRequestInterface $request) {
        $requestParams = new RequestParams();
        $login = $requestParams->getParsedBodyParam($request, 'login', FALSE);

        if ($login) {
            // používá názvy z konfigurace pro omezení množství našeptávaných jmen při vypl%nování formuláře v prohlížečích
            $fieldNameJmeno = Configuration::loginLogoutControler()['fieldNameJmeno'];
            $fieldNameHeslo = Configuration::loginLogoutControler()['fieldNameHeslo'];
            $loginJmeno = $requestParams->getParsedBodyParam($request, $fieldNameJmeno, FALSE);
            $loginHeslo = $requestParams->getParsedBodyParam($request, $fieldNameHeslo, FALSE);
            if ($loginJmeno AND $loginHeslo) {
                $loginAggregateEntity = $this->loginAggregateRepo->get($loginJmeno);
                if (isset($loginAggregateEntity) AND $this->authenticator->authenticate($loginAggregateEntity, $loginHeslo)) {  // z databáze
                    $this->statusSecurityRepo->get()->renewSecurityStatus($loginAggregateEntity);
                }
            }
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function logout(ServerRequestInterface $request) {
        $logout = (new RequestParams())->getParsedBodyParam($request, 'logout', FALSE);
        if ($logout) {
            $this->removeLoggedUser();  // bez parametru User
        }
        return $this->redirectSeeLastGet($request); // 303 See Other

    }

    private function removeLoggedUser() {
        $this->statusSecurityRepo->get()->renewSecurityStatus(null);
    }
}
