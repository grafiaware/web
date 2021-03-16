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
use Model\Repository\RegistrationRepo;
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
class ConfirmController extends StatusFrontControllerAbstract
{

    private $authenticator;

    /**
     *
     * @var RegistrationRepo
     */
    private $registrationRepo;
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
    RegistrationRepo $registrationRepo)
    {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->loginAggregateCredentialsRepo = $loginAggregateCredentialsRepo;
        $this->registrationRepo = $registrationRepo;
    }

    public function confirm(ServerRequestInterface $request, $uid) {              
          
            if ($uid ) {
                /** @var  LoginAggregateRegistration $loginAggregateRegistrationEntity  */
                $loginAggregateRegistrationEntity = $this->registrationRepo->getByUid($uid);
                
                
                
                if ( isset($loginAggregateRegistrationEntity) ) {                    
                    $passwordHash = $loginAggregateRegistrationEntity->getRegistration()->getPasswordHash();
                    $loginNameFk = $loginAggregateRegistrationEntity->getRegistration()->getLoginNameFk();   
                   // $uidRegistration = $loginAggregateRegistrationEntity->getRegistration()->getUid();  //nepotrebuji
                    
                    $credentials = new Credentials();
                    $credentials->setPasswordHash($passwordHash);
                    $credentials->setLoginNameFk($loginNameFk);                    
                     
                    /** @var  LoginAggregateCredentials $loginAggregateCredentialsEntity  */
                    $loginAggregateCredentialsEntity = $this->loginAggregateCredentialsRepo->get($loginNameFk);                      
                    if ( $loginAggregateCredentialsEntity->getCredentials() === \NULL  ) {
                        $loginAggregateCredentialsEntity->setCredentials($credentials);                                          
                    }
                    else {
                        // chyba Již bylo zaregistrováno a potvrzeno, nebudu to dělat znovu.
                        $this->addFlashMessage( "*confirm* chyba \n Již bylo zaregistrováno a potvrzeno, nebudu to dělat znovu.");
                    }
                    
                 } 
                 else {
                     //chyba Takový registrační požadavek nebyl požadovan/zaznamenán.
                     $this->addFlashMessage( "*confirm* chyba \n Takový registrační požadavek nebyl požadovan/zaznamenán.");
                 }
            }
        
        return $this->redirectSeeLastGet($request); // 303 See Other

    }
}