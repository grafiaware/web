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
use Model\Repository\LoginAggregateRegistrationRepo;
use Model\Repository\LoginAggregateCredentialsRepo;

use Model\Entity\Credentials;
use Model\Entity\LoginAggregateCredentials;
use Model\Entity\Registration;
use Model\Entity\LoginAggregateRegistration;
/**
 * Description of PostController
 *
 * @author pes2704
 */
class RegistrationController extends StatusFrontControllerAbstract
{

    private $authenticator;

    /**
     *
     * @var LoginAggregateRegistrationRepo
     */
    private $loginAggregateRegistrationRepo;
    /**
     *
     * @var LoginAggregateCredentialsRepo
     */
    private $loginAggregateCredentialsRepo;

    /**
     *
     */
    public function __construct(
                StatusSecurityRepo $statusSecurityRepo,
                   StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
     LoginAggregateCredentialsRepo $loginAggregateCredentialsRepo,
    LoginAggregateRegistrationRepo $loginAggregateRegistrationRepo,
            AuthenticatorInterface $authenticator)
    {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->loginAggregateCredentialsRepo = $loginAggregateCredentialsRepo;
        $this->loginAggregateRegistrationRepo = $loginAggregateRegistrationRepo;
        $this->authenticator = $authenticator;
    }

    public function register(ServerRequestInterface $request)
    {
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
                /** @var  LoginAggregateRegistration $loginAggregateRegistrationEntity  */
                $loginAggregateRegistrationEntity = $this->loginAggregateRegistrationRepo->get($registerJmeno);
                 // !!!! jeste hledat v tabulce registration, zda neni jmeno uz rezervovane
                if ( isset($loginAggregateRegistrationEntity) ) {
                     //  zaznam se jmenem jiz existuje, zmente jmeno---
                } else {
                     //verze 2
                     // ulozit udaje do tabulky, do ktere - registration + cas: do kdy je cekano na potvrzeni registrace
                     // protoze musi byt rezervace jmena nez potvrdi
                     //
                     // zobrazit "Dekujeme za Vasi registraci. Na vas email jsme vam odeslali odkaz, kterym registraci dokoncite. Odkaz je aktivni x hodin."
                     // poslat email s jmeno, heslo , +  "do x hodin potvrdte"
                     // jeste jeden mail "Registrace dokoncena."

                    //verze 1

                    $registration = new Registration();
                    $registration->setLoginNameFk($registerJmeno);
                    $registration->setPasswordHash( (new Password())->getPasswordHash($registerHeslo) );  // zahashované
                    $registration->setEmail($registerEmail);
                    /** @var  LoginAggregate $loginAggregateRegistrationEntity  */
                    $loginAggregateRegistrationEntity = new LoginAggregateRegistration();
                    $loginAggregateRegistrationEntity->setLoginName($registerJmeno);
                    $loginAggregateRegistrationEntity->setRegistration($registration);
                    $this->loginAggregateRegistrationRepo->add($loginAggregateRegistrationEntity);
                 }

            }

        }

        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    /**
     *
     * ###############
     * ### verze 1 ###
     * ###############
     *
     * @param ServerRequestInterface $request
     * @return type
     */
    public function register1(ServerRequestInterface $request)
    {
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
                /** @var  LoginAggregateCredentials $loginAggregateEntity  */
                $loginAggregateEntity = $this->loginAggregateCredentialsRepo->get($registerJmeno);
                if (! isset($loginAggregateEntity) ) {
                    $registerHesloHash = (new Password())->getPasswordHash($registerHeslo);
                    $credentials = new Credentials();
                    $credentials->setPasswordHash($registerHesloHash);
                    $credentials->setLoginNameFk($registerJmeno);

                    /** @var  LoginAggregate $loginAggregateEntity  */
                    $loginAggregateEntity = new LoginAggregateCredentials();
                    $loginAggregateEntity->setLoginName($registerJmeno);
                    $loginAggregateEntity->setCredentials($credentials);
                    $this->loginAggregateCredentialsRepo->add($loginAggregateEntity);
                 }
            }
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    
    
    public function confirm(ServerRequestInterface $request) {        
        $requestParams = new RequestParams();
        $confirm = $requestParams->getParsedBodyParam($request, 'confirm', FALSE);
        
        if ($confirm) {            
            $logJmeno = $requestParams->getParsedBodyParam($request, 'LOGNAME', FALSE);
          
            if ($logJmeno ) {
                /** @var  LoginAggregateRegistration $loginAggregateRegistrationEntity  */
                $loginAggregateRegistrationEntity = $this->loginAggregateRegistrationRepo->get($logJmeno);
                if ( isset($loginAggregateRegistrationEntity) ) {
                    
                    $passwordHash = $loginAggregateRegistrationEntity->getRegistration()->getPasswordHash();
                    $loginNameFk = $loginAggregateRegistrationEntity->getRegistration()->getLoginNameFk();
                                                             
                    $credentials = new Credentials();
                    $credentials->setPasswordHash($passwordHash);
                    $credentials->setLoginNameFk($loginNameFk);

                    /** @var  LoginAggregate $loginAggregateEntity  */
                    $loginAggregateEntity = new LoginAggregateCredentials();
                    $loginAggregateEntity->setLoginName($loginNameFk);
                    $loginAggregateEntity->setCredentials($credentials);
                      
                    $this->loginAggregateCredentialsRepo->add($loginAggregateEntity);
                 } 
                 else {
                     //chyba Takovy registracni pozadavek nebyl pozadovan/zaznamenan.
                 }
            }
        }
        return $this->redirectSeeLastGet($request); // 303 See Other

    }
}