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
use Model\Repository\LoginAggregateReadonlyRepo;

use Model\Entity\Credentials;
use Model\Entity\LoginAggregate;

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

    private $loginAggregateRepo;

    /**
     *
     */
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            LoginAggregateReadonlyRepo $loginAggregateRepo,
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


    public function register(ServerRequestInterface $request) {
        $requestParams = new RequestParams();
        $register = $requestParams->getParsedBodyParam($request, 'register', FALSE);

        if ($register) {
            $fieldNameJmeno = Configuration::loginLogoutControler()['fieldNameJmeno'];
            $fieldNameHeslo = Configuration::loginLogoutControler()['fieldNameHeslo'];
            $fieldNameEmail = Configuration::loginLogoutControler()['fieldNameEmail'];

            $registerJmeno = $requestParams->getParsedBodyParam($request, $fieldNameJmeno, FALSE);
            $registerHeslo = $requestParams->getParsedBodyParam($request, $fieldNameHeslo, FALSE);
            $registerEmail = $requestParams->getParsedBodyParam($request, $fieldNameEmail, FALSE);

            if ($registerJmeno AND $registerHeslo AND  $registerEmail ) {
                /** @var  Credentials $credentialsEntity  */
                $credentialsEntity = $this->loginAggregateRepo->get($registerJmeno);
                 // !!!! jeste hledat v tabulce registration, zda neni jmeno uz rezervovane
                if ( $credentialsEntity ) {
                     //  zaznam se jmenem jiz existuje, zmente jmeno---
                }else {
                     //verze 2
                     // ulozit udaje do tabulky, do ktere - registration??? + cas: do kdy je cekano na potvrzeni registrace
                     // protoze musi byt rezervace jmena nez potvrdi
                     //
                     // zobrazit "Dekujeme za Vasi registraci. Na vas email jsme vam odeslali odkaz, kterym registraci dokoncite. Odkaz je aktivni x hodin."
                     // poslat email s jmeno, heslo , +  "do x hodin potvrdte"
                     // jeste jeden mail "Registrace dokoncena."

                    //verze 1
                    /** @var  Credentials $credentialsEntity  */
                    $credentialsEntity = new Credentials();
                    $credentialsEntity->setLoginNameFk($registerJmeno);
                    $credentialsEntity->setEmail($registerEmail);

                    $passwordObjekt = new Password();
                    $registerHesloHash = $passwordObjekt->getPasswordHash($registerHeslo);
                    $credentialsEntity->setPasswordHash($registerHesloHash);

                    //$credentialsEntity->setPersisted();

                    $this->loginAggregateRepo->add($credentialsEntity);
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
