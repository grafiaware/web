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

// controller
use Controller\StatusFrontControllerAbstract;

// model
use Model\Repository\StatusPresentationRepo;
use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusFlashRepo;
use Model\Repository\CredentialsRepo;

/**
 * Description of PostController
 *
 * @author pes2704
 */
class LoginLogoutController extends StatusFrontControllerAbstract {

    // konstanty individualizované pro jeden web
    const JMENO_FIELD_NAME = "jmenowwwgrafia";
    const HESLO_FIELD_NAME = "heslowwwgrafia";

    private $authenticator;

    private $credentialsRepo;

    /**
     *
     */
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            CredentialsRepo $credentialsRepo,
            AuthenticatorInterface $authenticator) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->credentialsRepo = $credentialsRepo;
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
                $credentialsEntity = $this->credentialsRepo->get($loginJmeno);
                if (isset($credentialsEntity) AND $this->authenticator->authenticate($credentialsEntity, $loginHeslo)) {  // z databáze
                    $this->statusSecurityRepo->get()->renewSecurityStatus($credentialsEntity);
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
