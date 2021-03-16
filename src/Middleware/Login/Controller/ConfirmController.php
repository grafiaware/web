<?php

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
                $registrationEntity = $this->registrationRepo->getByUid($uid);                
                
                if ( isset($registrationEntity) ) {                    
                    $passwordHash = $registrationEntity->getPasswordHash();
                    $loginNameFk = $registrationEntity->getLoginNameFk();   
                   // $uidRegistration = $loginAggregateRegistrationEntity->getRegistration()->getUid();  //nepotrebuji
                    
                    $credentials = new Credentials();
                    $credentials->setPasswordHash($passwordHash);
                    $credentials->setLoginNameFk($loginNameFk);                                         
                    /** @var  LoginAggregateCredentials $loginAggregateCredentialsEntity  */
                    $loginAggregateCredentialsEntity = $this->loginAggregateCredentialsRepo->get($loginNameFk);                      
                    if ( $loginAggregateCredentialsEntity->getCredentials() === \NULL  ) {
                        $loginAggregateCredentialsEntity->setCredentials($credentials); 
                        
                        $this->addFlashMessage( "Potvrzeno, Vaše registrace byla dokončena.");
                        // **mail** !!!!
                    }
                    else {
                        // chyba Již bylo zaregistrováno a potvrzeno.
                        // nehlasit nic, nebo az po 10sec  poslat               **mail** ???!!!!
                        // $this->addFlashMessage( "Byli jste zaregistrováni.");
                    }                    
                 } 
                 else {
                     // chyba Takový registrační požadavek nebyl požadovan/zaznamenán.
                     // zapsat do logu, tj. nějak dát vědět autorům systému                     
                     // $this->addFlashMessage( "Takový registrační požadavek nebyl požadován/zaznamenán.");
                 }
            }
        
        return $this->redirectSeeLastGet($request); // 303 See Other

    }
}