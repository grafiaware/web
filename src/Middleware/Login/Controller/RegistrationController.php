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
use Model\Repository\LoginAggregateCredentialsRepo;

use Model\Entity\Credentials;
use Model\Entity\LoginAggregateCredentials;

/**
 * Description of PostController
 *
 * @author pes2704
 */
class RegistrationController extends StatusFrontControllerAbstract {

    private $authenticator;

    private $loginAggregateRepo;

    /**
     *
     */
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            LoginAggregateCredentialsRepo $loginAggregateRepo,
            AuthenticatorInterface $authenticator) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->loginAggregateRepo = $loginAggregateRepo;
        $this->authenticator = $authenticator;
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
                /** @var  Credentials $loginAggregateEntity  */
                $loginAggregateEntity = $this->loginAggregateRepo->get($registerJmeno);
                 // !!!! jeste hledat v tabulce registration, zda neni jmeno uz rezervovane
                if ( isset($loginAggregateEntity) ) {
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

                    $passwordObjekt = new Password();
                    $registerHesloHash = $passwordObjekt->getPasswordHash($registerHeslo);
                    $credentials = new Credentials();
                    $credentials->setPasswordHash($registerHesloHash);
                    $credentials->setLoginNameFk($registerJmeno);

                    /** @var  LoginAggregate $loginAggregateEntity  */
                    $loginAggregateEntity = new LoginAggregateCredentials();
                    $loginAggregateEntity->setLoginName($registerJmeno);
                    $loginAggregateEntity->setCredentials($credentials);

                    $this->loginAggregateRepo->add($loginAggregateEntity);
                 }

            }

        }

        return $this->redirectSeeLastGet($request); // 303 See Other

    }
}
