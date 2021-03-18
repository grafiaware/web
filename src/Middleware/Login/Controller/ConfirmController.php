<?php

namespace Middleware\Login\Controller;

use Site\Configuration;
use Mail\Mail;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Request\RequestParams;
use Security\Auth\AuthenticatorInterface;
use Pes\Security\Password\Password;

// controller
use Controller\PresentationFrontControllerAbstract;

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
class ConfirmController extends PresentationFrontControllerAbstract
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
            ResourceRegistryInterface $resourceRegistry=null,
     LoginAggregateCredentialsRepo $loginAggregateCredentialsRepo,
                  RegistrationRepo $registrationRepo)
    {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->loginAggregateCredentialsRepo = $loginAggregateCredentialsRepo;
        $this->registrationRepo = $registrationRepo;
    }

    public function confirm(ServerRequestInterface $request, $uid) {
            if ($uid ) {
                        // /* @var  LoginAggregateRegistration $loginAggregateRegistrationEntity  */
                /** @var  LoginAggregateRegistration $registrationEntity  */
                $registrationEntity = $this->registrationRepo->getByUid($uid);                
                
                if ( isset($registrationEntity) ) {                    
                    $password = $registrationEntity->getPasswordHash();  // v registration je nezahash. heslo
                    $loginNameFk = $registrationEntity->getLoginNameFk();   
                    // $uidRegistration = $loginAggregateRegistrationEntity->getRegistration()->getUid();  //nepotrebuji
                    
                    $credentials = new Credentials();
                    $credentials->setPasswordHash( (new Password())->getPasswordHash($password) );
                    $credentials->setLoginNameFk($loginNameFk);                                         
                    /** @var  LoginAggregateCredentials $loginAggregateCredentialsEntity  */
                    $loginAggregateCredentialsEntity = $this->loginAggregateCredentialsRepo->get($loginNameFk);                      
                    if ( $loginAggregateCredentialsEntity->getCredentials() === \NULL  ) {
                        $loginAggregateCredentialsEntity->setCredentials($credentials);   
                        
                        $registrationEntity->setPasswordHash('');
                        
                        /* nebude */$this->addFlashMessage( "* nebude * \n Potvrzeno, Vaše registrace byla dokončena.");
                        // **mail** !!!!
                        //Vaše registrace byla dokončena. Děkujeme Vám za spolupráci.
                        $mail = new Mail();
                        $mail->mail('body_confirm');
                    }
                    else {
                        // chyba Již bylo zaregistrováno a potvrzeno.
                        // nehlasit nic,        ?? nebo az po 10sec  poslat    **mail** ??? ??
                        /* nebude */ $this->addFlashMessage( " * nwebude * \n Byli jste zaregistrováni.");
                    }                    
                 } 
                 else {
                     // chyba Takový registrační požadavek nebyl požadovan/zaznamenán.
                     // zapsat do logu, tj. nějak dát vědět autorům systému                     
                      /* nebude */$this->addFlashMessage( " *nebude* \n Takový registrační požadavek nebyl požadován/zaznamenán.");
                 }
            }
        
        return $this->redirectSeeLastGet($request); // 303 See Other

    }
}