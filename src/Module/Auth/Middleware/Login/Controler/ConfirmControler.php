<?php
namespace Auth\Middleware\Login\Controler;

use Site\ConfigurationCache;
use Mail\MailSettings;
use Access\Enum\RoleEnum;

use Pes\Mail\MailInterface;
use Mail\MessageFactory\HtmlMessage;
use Pes\Mail\Assembly;
use Pes\Mail\Assembly\{Content, Attachment, Party};

use Psr\Http\Message\ServerRequestInterface;

use Pes\Core\Security\Password\Password;

// model
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;

use Auth\Model\Repository\RegistrationRepo;
use Auth\Model\Repository\LoginAggregateCredentialsRepo;

use Auth\Model\Entity\Credentials;
use Auth\Model\Entity\LoginAggregateCredentials;
use Auth\Model\Entity\Registration;

/**
 * Description of ConfirmControler
 *
 * @author pes2704
 */
class ConfirmControler extends LoginControlerAbstract
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

                        // nové Credentials - se zahashovaným heslem, default rolí z konfigurace
                        $credentials = new Credentials();
                        $credentials->setPasswordHash( (new Password())->getPasswordHash($password) );
                        $credentials->setLoginNameFk($loginNameFk);
                        $credentials->setRoleFk(RoleEnum::VISITOR);
                        $loginAggregateCredentialsEntity->setCredentials($credentials);
                     
                        // vymazání hesla z registrace -- viz nize
                        //$registrationEntity->setPasswordHash(''); jeste potrebuji nize do druheho mailu

                        $this->addFlashMessage( "Potvrzeno, Vaše registrace byla dokončena.");

                        #########################--------- poslat mail -------------------
                        /** @var MailInterface $mail */
                        $mail = $this->container->get(MailInterface::class);
                        /** @var HtmlMessage $mailMessageFactory */
                        $mailMessageFactory = $this->container->get(HtmlMessage::class);

                            $attachments = [ (new Attachment())
    //                                        ->setFileName(ConfigurationCache::mail()['mail.attachments'].'logo_grafia.png')
    //                                        ->setAltText('Logo Grafia')
                                           ];
                            $mailSettings = MailSettings::fromConfiguration();
                            $data_logo_grafia = $mailSettings->logoDataUri('grafia');
         
                        if ($registrationEntity->getInfo() ) {
                            $subject =  'Veletrh práce a vzdělávání - Vaše registrace ';
                            $body = $mailMessageFactory->create(__DIR__."/Messages/confirmRepre1.php",
                                                            [  'data_logo_grafia' => $data_logo_grafia,
                                                              // 'logo_grafia'   => 'logo_grafia.png'
                                                            ] );                                  
                        } else {                        
                            $subject =  'Veletrh práce a vzdělávání - Registrace dokončena.';                            
                            $body = $mailMessageFactory->create(__DIR__."/Messages/confirm.php",
                                                            [  'data_logo_grafia' => $data_logo_grafia,
                                                              // 'logo_grafia'   => 'logo_grafia.png'
                                                            ] );                        
                        }    
                            $params = (new Assembly())
                                    ->setContent(  (new Content())
                                                 ->setSubject($subject)
                                                 ->setHtml($body)
             //                                    ->setAttachments($attachments)
                                                 )
                                    ->setParty  (  $mailSettings->party('info')
                                                 ->addTo( $registerEmail, $loginNameFk)
                                                );
                        $mail->mail($params); // posle mail                        
                       // ---------------------------------------------------------------------------------
                        
                        if ($registrationEntity->getInfo() ) {
                            ##########################--------- poslat mail do Grafie - registrujicise  je  vystavovatel -------------------
                            /** @var MailInterface $mail */
                            $mail = $this->container->get(MailInterface::class);
                            /** @var HtmlMessage $mailMessageFactory */
                            $mailMessageFactory = $this->container->get(HtmlMessage::class);                        

                            $subject =  'Veletrh práce a vzdělávání - Registrace zástupce vystavovatele.';
                            $body = $mailMessageFactory->create(__DIR__."/Messages/registrationexhib.php",
                                                                ['registerJmeno' =>  $loginNameFk,
                                                                 'registerHeslo' =>  $password ,
                                                                 'registerEmail' =>  $registerEmail,
                                                                 'registerInfo' =>   $registrationEntity->getInfo(),
                                                                ]);
                            $attachments = [ (new Attachment())
    //                                        ->setFileName(ConfigurationCache::mail()['mail.attachments'].'logo_grafia.png')
    //                                        ->setAltText('Logo Grafia')
                                           ];
                            $params = (new Assembly())
                                        ->setContent(  (new Content())
                                                         ->setSubject($subject)
                                                         ->setHtml($body)
        //                                                 ->setAttachments($attachments)   //zde zbytecne
                                                    )
                                        ->setParty  (  $mailSettings->addRecipients(
                                                         $mailSettings->addRecipients($mailSettings->party('noreply'), 'to', 'exhibitorRegistration.to'),
                                                         'cc', 'exhibitorRegistration.cc')
                                                    );
                            $mail->mail($params); // posle mail
                            #########################-----------------------------
                        }
                        
                        // vymazání hesla z registrace
                        $registrationEntity->setPasswordHash('');                                             
                    } //neni credentials - tak ho vytvoril
                    
                    else {
                        // stav, kdy -  Již bylo zaregistrováno a potvrzeno.
                        // nehlasit nic ?
                        $this->addFlashMessage( "Byli jste již zaregistrováni.");
                    }
                 }
                 else {
                     // stav, kdy -  Takový registrační požadavek nebyl požadovan/zaznamenán.
                     // zapsat do logu 
                     //$this->addFlashMessage( "Takový registrační požadavek nebyl požadován/zaznamenán.");
                 }
            }

//        return $this->redirectSeeOther($request, 'web/v1/static/dokonceni-registrace'); // 303 See Other
        return $this->createResponseRedirectSeeOther($request, ''); // 303 See Other

    }
}