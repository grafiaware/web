<?php

namespace Auth\Middleware\Login\Controler;

use Site\ConfigurationCache;
use Mail\MailSettings;

use Pes\Mail\MailInterface;
use Mail\MessageFactory\HtmlMessage;
use Pes\Mail\Assembly;
use Pes\Mail\Assembly\{Content, Attachment, Party};

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Request\RequestParams;
use Pes\Core\Security\Password\Password;

// model
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;

//use Auth\Model\Repository\LoginAggregateCredentialsRepo;
//use Auth\Model\Entity\LoginAggregateCredentials;
//use Auth\Model\Repository\LoginAggregateRegistrationRepo;
//use Auth\Model\Entity\LoginAggregateRegistration;
use Auth\Model\Repository\LoginAggregateFullRepo;

use Auth\Model\Entity\LoginAggregateFullInterface;
use Auth\Authenticator\AuthenticatorInterface;

use Auth\Model\Entity\CredentialsInterface;
use Auth\Model\Entity\RegistrationInterface;

/**
 * Description of passwordControler
 *
 * @author vlse2610
 */
class PasswordControler extends LoginControlerAbstract {
//    private $loginAggregateCredentialsRepo;
//    private $loginAggregateRegistrationRepo;

    private $loginAggregateFullRepo;
    private $authenticator;


    public function __construct(
                        StatusSecurityRepo $statusSecurityRepo,
                           StatusFlashRepo $statusFlashRepo,
                    StatusPresentationRepo $statusPresentationRepo,
//             LoginAggregateCredentialsRepo $loginAggregateCredentialRepo,
//            LoginAggregateRegistrationRepo $loginAggregateRegistrationRepo,
            LoginAggregateFullRepo $loginAggregateFullRepo,
                    AuthenticatorInterface $authenticator)
    {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
//        $this->loginAggregateCredentialsRepo = $loginAggregateCredentialRepo;
//        $this->loginAggregateRegistrationRepo = $loginAggregateRegistrationRepo;
        $this->loginAggregateFullRepo = $loginAggregateFullRepo;        
        $this->authenticator = $authenticator;
    }


    public function forgottenPassword(ServerRequestInterface $request) {
        $requestParams = new RequestParams();
        $forgottenpassword = $requestParams->getParsedBodyParam($request, 'forgottenpassword', FALSE);

        if ($forgottenpassword) {
            // používá názvy z konfigurace pro omezení množství našeptávaných jmen při vypl%nování formuláře v prohlížečích
            $fieldNameJmeno = ConfigurationCache::auth()['fieldNameJmeno'];
            $loginJmeno = $requestParams->getParsedBodyParam($request, $fieldNameJmeno, FALSE);
            $loginAggregateFull = $this->loginAggregateFullRepo->get($loginJmeno);

            if (isset($loginAggregateFull) ) {
                // heslo je zahashovane v Credentials - posleme mailem náhradní vygenerované, a zahashujeme do Credentials               
                   /** @var CredentialsInterface  $credentials */
                  /** @var RegistrationInterface  $registration */ 
                $credentials = $loginAggregateFull->getCredentials();
                $registration = $loginAggregateFull->getRegistration();  
                if  (isset($credentials) AND isset($registration))  {
                    $generatedPassword = $this->generatePassword();
                    $hashedGeneratedPassword = ( new Password())->getPasswordHash($generatedPassword);
                    $credentials->setPasswordHash( $hashedGeneratedPassword );

                    $registerEmail = $registration->getEmail();
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
                    $subject =  'Veletrh práce a vzdělávání - Nové heslo.';
                    $body = $mailMessageFactory->create(__DIR__."/Messages/forgottenpassword.php",
                                                        ['loginJmeno'=>$loginJmeno,
                                                         'generatedPassword'=>$generatedPassword,
                                                         'data_logo_grafia' => $data_logo_grafia
                                                        ]);                    
                    $params = (new Assembly())
                                ->setContent(  (new Content())
                                             ->setSubject($subject)
                                             ->setHtml($body)
                                           //  ->setAttachments($attachments)
                                            )
                                ->setParty  (  $mailSettings->party('noreply')
                                            // ->addReplyTo('svoboda@grafia.cz', 'reply veletrhprace.online')
                                            ->addTo( $registerEmail, $loginJmeno )
                                            //->addTo('selnerova@grafia.cz', 'vlse') 
                                            // ->addCc($ccAddress, $ccName)   // ->addBcc($bccAddress, $bccName)
                                            );
                    $mail->mail($params); // posle mail
                    #########################-----------------------------

                    $this->addFlashMessage("Na Vaši email.adresu jsme odeslali nové přihlašovací údaje.");
                } else {
                    $this->addFlashMessage("Váš účet nebyl zaregistrován, neznáme Vaši email adresu. Nelze vám zaslat nové přihlašovací údaje.");
                }
            } else {
                $this->addFlashMessage("Neplatné jméno!");
            }
        }

        return $this->redirectSeeLastGet($request); // 303 See Other
    }




    private function generatePassword () : string {
        $length = 8;
        $emailPattern = "(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{".$length.",}";
        //Heslo musí obsahovat nejméně jedno velké písmeno, jedno malé písmeno a jednu číslici. Jiné znaky než písmena a číslice nejsou povoleny. Délka musí být nejméně 8 znaků.";

        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';  // bez nuly
        $count = 0;
        do {
            $psw = substr(str_shuffle(str_repeat($chars,$length)),0,$length);
            $ok = preg_match("/$emailPattern/", $psw);
            $count++;
        } while (!$ok);

        return $psw;
    }


    public function changePassword(ServerRequestInterface $request) {
        $loginAggregateFullFromStatus = $this->statusSecurityRepo->get()->getLoginAggregate();
        if(!isset($loginAggregateFullFromStatus)) {
            return $this->createUnauthorizedResponse();
        }      
        
        $requestParams = new RequestParams();
        $changePassword = $requestParams->getParsedBodyParam($request, 'changepassword', FALSE);  // hodnota z button
        if ($changePassword) {
            $fieldNameHesloStare = ConfigurationCache::auth()['fieldNameHesloStare'];
            $fieldNameHeslo = ConfigurationCache::auth()['fieldNameHeslo'];
            $oldHeslo = $requestParams->getParsedBodyParam($request, $fieldNameHesloStare, FALSE);
            $changeHeslo = $requestParams->getParsedBodyParam($request, $fieldNameHeslo, FALSE);
        }
        if ($oldHeslo AND $changeHeslo) {
            /** @var  LoginAggregateFullInterface $loginAggregateFullFromStatus  */
            $agree =  $this->authenticator->authenticate($loginAggregateFullFromStatus, $oldHeslo);
            if ($agree) {
                $hashedChangedPassword = ( new Password())->getPasswordHash( $changeHeslo );
                
                $loginName = $loginAggregateFullFromStatus->getLoginName();
                $loginAggregateFull = $this->loginAggregateFullRepo->get($loginName);
                    /** @var CredentialsInterface  $credentials */
                $credentials = $loginAggregateFull->getCredentials();
                $credentials->setPasswordHash($hashedChangedPassword );
                
                $loginAggregateFullFromStatus->getCredentials()->setPasswordHash($hashedChangedPassword);
                $this->addFlashMessage("Vaše heslo bylo změněno.");
            } else {
                $this->addFlashMessage("Nesouhlasí vaše staré heslo.");                    
            }
        }

        return $this->redirectSeeLastGet($request); // 303 See Other
    }

}
