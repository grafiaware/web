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
use Model\Repository\Exception\UnableAddEntityException;

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
                 
                if ( isset($loginAggregateRegistrationEntity) ) {
                    $this->addFlashMessage("Záznam se zadaným jménem jiz existuje. Zadejte jiné jméno!");
                } else {
                     //verze 2
                     // ulozit udaje do tabulky, do registration + cas: do kdy je cekano na potvrzeni registrace
                     // protoze musi byt rezervace jmena nez potvrdi v mailu
                     //
                     // zobrazit "Dekujeme za Vasi registraci. Na vas email jsme vam odeslali odkaz, kterym registraci dokoncite. Odkaz je aktivni x hodin."
                     // poslat email s jmeno, heslo , +  "do x hodin potvrdte"
                     // potvrzenim = klikem na odkaz v mailu  se provede confirm()
                     // jeste jeden mail "Registrace dokoncena."

                    //verze 1

                    $registration = new Registration();
                    $registration->setLoginNameFk($registerJmeno);
                    $registration->setPasswordHash( (new Password())->getPasswordHash($registerHeslo) );  // zahashované
                    $registration->setEmail($registerEmail);
                          //$registration->setEmailTime( new \DateTime() ); //ted ne
                    /** @var  LoginAggregate $loginAggregateRegistrationEntity  */
                    $loginAggregateRegistrationEntity = new LoginAggregateRegistration();
                    $loginAggregateRegistrationEntity->setLoginName($registerJmeno);
                    $loginAggregateRegistrationEntity->setRegistration($registration);
                    try {
                        $this->loginAggregateRegistrationRepo->add($loginAggregateRegistrationEntity);
                    } catch (UnableAddEntityException $unableExc){
                        //dej nové jméno.
                        $this->addFlashMessage("Záznam se zadaným jménem jiz existuje. Zadejte jiné jméno!");
                    }                    
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
                /** @var  LoginAggregateCredentials $loginAggregateCeredentialsEntity  */
                $loginAggregateCeredentialsEntity = $this->loginAggregateCredentialsRepo->get($registerJmeno);
                if (! isset($loginAggregateCeredentialsEntity) ) {
                    $registerHesloHash = (new Password())->getPasswordHash($registerHeslo);
                    $credentials = new Credentials();
                    $credentials->setPasswordHash($registerHesloHash);
                    $credentials->setLoginNameFk($registerJmeno);

                    /** @var  LoginAggregate $loginAggregateCeredentialsEntity  */
                    $loginAggregateCeredentialsEntity = new LoginAggregateCredentials();
                    $loginAggregateCeredentialsEntity->setLoginName($registerJmeno);
                    $loginAggregateCeredentialsEntity->setCredentials($credentials);
                    $this->loginAggregateCredentialsRepo->add($loginAggregateCeredentialsEntity);
                 } else {
                     $this->addFlashMessage("Záznam se zadaným jménem jiz existuje. Zadejte jiné jméno!");
                 }
            }
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    
    
    public function confirm(ServerRequestInterface $request) {        
        $requestParams = new RequestParams();
        //$confirm = $requestParams->getParsedBodyParam($request, 'confirm', FALSE);
        //$confirm = $requestParams->getParam($request, 'LOGNAME', FALSE);
        $logJmeno = $requestParams->getParam($request, 'LOGNAME', FALSE);
                  
        //$logJmeno = $requestParams->getParsedBodyParam($request, 'LOGNAME', FALSE);
          
            if ($logJmeno ) {
                /** @var  LoginAggregateRegistration $loginAggregateRegistrationEntity  */
                $loginAggregateRegistrationEntity = $this->loginAggregateRegistrationRepo->get($logJmeno);
                if ( isset($loginAggregateRegistrationEntity) ) {                    
                    $passwordHash = $loginAggregateRegistrationEntity->getRegistration()->getPasswordHash();
                    $loginNameFk = $loginAggregateRegistrationEntity->getRegistration()->getLoginNameFk();                                                             
                    $credentials = new Credentials();
                    $credentials->setPasswordHash($passwordHash);
                    $credentials->setLoginNameFk($loginNameFk);
                    /** @var  LoginAggregateCredentials $loginAggregateCredentialsEntity  */
                    $loginAggregateCredentialsEntity = new LoginAggregateCredentials();
                    $loginAggregateCredentialsEntity->setLoginName($loginNameFk);
                    $loginAggregateCredentialsEntity->setCredentials($credentials);
                      
                    $this->loginAggregateCredentialsRepo->add($loginAggregateCredentialsEntity);
                 } 
                 else {
                     //chyba Takovy registracni pozadavek nebyl pozadovan/zaznamenan.
                 }
            }
        
        return $this->redirectSeeLastGet($request); // 303 See Other

    }
}