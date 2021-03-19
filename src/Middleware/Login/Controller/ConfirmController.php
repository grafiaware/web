<?php

namespace Middleware\Login\Controller;

use Site\Configuration;
use Mail\Mail;

use Mail\Params;
use Mail\Params\{Content, Attachment, Party};

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
     * @var LoginAggregateCredentialsRepo
     */
    private $loginAggregateCredentialsRepo;
    /**
     *
     * @var RegistrationRepo
     */
    private $registrationRepo;    
    

    /**
     *
     */
    public function __construct(
                StatusSecurityRepo $statusSecurityRepo,
                   StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
         ResourceRegistryInterface $resourceRegistry=null,
     LoginAggregateCredentialsRepo $loginAggregateCredentialsRepo,
                  RegistrationRepo $registrationRepo )
    {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->loginAggregateCredentialsRepo = $loginAggregateCredentialsRepo;
        $this->registrationRepo = $registrationRepo;
    }

    public function confirm(ServerRequestInterface $request, $uid) {
            if ( $uid ) {
                /** @var  Registration $registrationEntity  */
                $registrationEntity = $this->registrationRepo->getByUid($uid);                
                
                if ( isset($registrationEntity) ) {                    
                    $password = $registrationEntity->getPasswordHash();  // v registration je nezahash. heslo
                    $loginNameFk = $registrationEntity->getLoginNameFk();   
                    $registerEmail = $registrationEntity->getEmail();
                                                         
                    /** @var  LoginAggregateCredentials $loginAggregateCredentialsEntity  */
                    $loginAggregateCredentialsEntity = $this->loginAggregateCredentialsRepo->get($loginNameFk);                      
                    if ( $loginAggregateCredentialsEntity->getCredentials() === \NULL  ) {
                        $credentials = new Credentials();
                        $credentials->setPasswordHash( (new Password())->getPasswordHash($password) );
                        $credentials->setLoginNameFk($loginNameFk);                            
                        $loginAggregateCredentialsEntity->setCredentials($credentials);   
                        
                        $registrationEntity->setPasswordHash('');                        
                        
/* nebude */$this->addFlashMessage( "Potvrzeno, Vaše registrace byla dokončena.");                                                                                          
                   
                        #########################--------- poslat mail -------------------                   
                        /** @var Mail $mail */
                        $mail = $this->container->get(Mail::class);
                        $subject =  'Veletrh práce a vzdělávání - Registrace dokončena.';
                        $body  =
                        "<h3> Veletrh práce a vzdělávání </h3>"         
                        ."<p> Vaše registrace byla dokončena. Děkujeme Vám za spolupráci.  </p> "
                        ."<br/><p>S pozdravem <br/> tým realizátora Grafia,s.r.o.</p>" ;

                        $attachments = [ (new Attachment())
                                        ->setFileName(Configuration::mail()['mail.files.directory'].'logo_grafia.png')  // /_www_vp_files/attachments/
                                        ->setAltText('Logo Grafia')
                                       ];
                        $params = (new Params()) 
                                    ->setContent(  (new Content())
                                                 ->setSubject($subject)
                                                 ->setBody($body)
             //                                    ->setAttachments($attachments)
                                                 )
                                    ->setParty  (  (new Party())
                                                 ->setFrom('info@najdisi.cz', 'veletrhprace.online')
                                                 ->addReplyTo('svoboda@grafia.cz', 'reply veletrhprace.online')
                                                 ->addTo( $registerEmail, $loginNameFk)
                                          //->addTo('selnerova@grafia.cz', 'vlse')  // ->addCc($ccAddress, $ccName)   // ->addBcc($bccAddress, $bccName)                                                
                                                );
                        $mail->mail($params); // posle mail
                        #########################-----------------------------                                               
                        
                    }
                    else {
                        // stav, kdy -  Již bylo zaregistrováno a potvrzeno.
                        // nehlasit nic,        ?? nebo az po 10sec  poslat    **mail** ??? ??
                        $this->addFlashMessage( "Byli jste zaregistrováni.");
                    }                    
                 } 
                 else {
                     // stav, kdy -  Takový registrační požadavek nebyl požadovan/zaznamenán.
                     // zapsat do logu, tj. nějak dát vědět autorům systému                     
                     $this->addFlashMessage( "Takový registrační požadavek nebyl požadován/zaznamenán.");
                 }
            }
        
        return $this->redirectSeeOther($request, 'www/item/static/dokonceni-registrace'); // 303 See Other

    }
}