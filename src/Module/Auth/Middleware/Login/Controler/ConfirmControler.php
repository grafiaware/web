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
use Auth\Model\Repository\LoginAggregateFullRepo;
use Auth\Model\Dao\CredentialsDao;
use Pes\Model\RowData\RowData;
use Status\Model\Enum\FlashSeverityEnum;
use Psr\Http\Message\ResponseInterface;

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
            if ($this->featureFlags()->isEnabled('vp.registrace-emailem')) {
                return $this->confirmPage($request, (string) $uid);
            }
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

    /**
     * A1: GET potvrzovacího odkazu jen ukáže tlačítko – registraci dokončí až POST (skenery pošty odkazy otevírají).
     */
    private function confirmPage(ServerRequestInterface $request, string $uid): ResponseInterface {
        $registration = $uid !== '' ? $this->registrationRepo->getByUid($uid) : null;
        if (!isset($registration)) {
            return $this->createActionPageResponse($request, 'Odkaz není platný',
                'Tento odkaz pro dokončení registrace neznáme. Zkontrolujte, že jste ho z e-mailu zkopírovali celý.');
        }
        if ($this->loginAggregateCredentialsRepo->get($registration->getLoginNameFk())?->getCredentials() !== null) {
            return $this->createActionPageResponse($request, 'Registrace je dokončená',
                'Tuto registraci jste už dokončili. Přihlaste se e-mailem a heslem nebo si nechte poslat přihlašovací odkaz.');
        }
        return $this->createActionPageResponse($request, 'Dokončení registrace',
            "Dokončete registraci pro e-mail {$registration->getEmail()}. Po dokončení budete přihlášeni.",
            "auth/v1/confirm/{$uid}", 'Dokončit registraci');
    }

    /**
     * A1: POST potvrzení – vytvoří credentials (heslo zahashuje, bez hesla prázdné), pošle e-maily a uživatele přihlásí.
     */
    public function confirmAndLogin(ServerRequestInterface $request, $uid): ResponseInterface {
        $registration = $uid ? $this->registrationRepo->getByUid((string) $uid) : null;
        if (!isset($registration)) {
            $this->addFlashMessage("Odkaz pro dokončení registrace není platný.", FlashSeverityEnum::WARNING);
            return $this->createResponseRedirectSeeOther($request, '');
        }
        $loginName = $registration->getLoginNameFk();
        $email = (string) $registration->getEmail();
        $loginAggregateCredentials = $this->loginAggregateCredentialsRepo->get($loginName);
        if ($loginAggregateCredentials->getCredentials() !== null) {
            $this->addFlashMessage("Registrace už byla dokončena. Přihlaste se.");
            return $this->createResponseRedirectSeeOther($request, '');
        }

        $password = $registration->getPasswordHash();  // do potvrzení nezahashované heslo, '' = bez hesla
        $hasPassword = $password !== '';
        // zápis přímo přes Dao – ihned v DB, aby šlo uživatele v tomtéž requestu přihlásit
        $credentialsRow = new RowData();
        $credentialsRow->import([
            'login_name_fk' => $loginName,
            'password_hash' => $hasPassword ? (new Password())->getPasswordHash($password) : '',
            'role_fk' => RoleEnum::VISITOR,
        ]);
        $this->container->get(CredentialsDao::class)->insert($credentialsRow);
        $registration->setPasswordHash('');   // čitelné heslo z registrace pryč (zapíše flush repository)
        $this->registrationRepo->flush();

        $info = $registration->getInfo();
        $exhibitor = $info !== null && trim($info) !== '';
        $mailer = $this->authMailer();
        $mailer->sendRegistrationDone($email, $loginName, $hasPassword, $exhibitor);
        if ($exhibitor) {
            $mailer->sendExhibitorRegistration($loginName,
                $hasPassword ? $password : '(bez hesla – přihlašuje se odkazem z e-mailu)', $email, $info);
        }

        $loginAggregateFull = $this->container->get(LoginAggregateFullRepo::class)->get($loginName);
        if (isset($loginAggregateFull)) {
            $this->statusSecurityRepo->get()->newContext($loginAggregateFull);
            $this->addFlashMessage("Registrace je dokončená, jste přihlášeni.", FlashSeverityEnum::SUCCESS);
        } else {
            $this->addFlashMessage("Registrace je dokončená. Přihlaste se.");
        }
        return $this->createResponseRedirectSeeOther($request, '');
    }
}
