<?php

namespace Auth\Middleware\Login\Controler;

use Site\ConfigurationCache;

use Mail\Mail;
use Mail\MessageFactory\HtmlMessage;
use Mail\Params;
use Mail\Params\{Content, Attachment, Party};

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Request\RequestParams;
use Pes\Security\Password\Password;

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

            if ($loginJmeno ) {
                // heslo je zahashovane v Credentials - posleme mailem náhradní vygenerované, a zahashujeme do Credentials
                /** @var  LoginAggregateCredentials $loginAggregateCredentialsEntity  */
               
                $loginAggregateFull = $this->loginAggregateFullRepo->get($loginJmeno);
                
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
                    /** @var Mail $mail */
                    $mail = $this->container->get(Mail::class);
                    /** @var HtmlMessage $mailMessageFactory */
                    $mailMessageFactory = $this->container->get(HtmlMessage::class);

                    $attachments = [ (new Attachment())
//                                        ->setFileName(ConfigurationCache::mail()['mail.attachments'].'logo_grafia.png')
//                                        ->setAltText('Logo Grafia')
                                   ];                        
                    $data_logo_grafia="data:image/gif;base64,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" ;
                    $subject =  'Veletrh práce a vzdělávání - Nové heslo.';
                    $body = $mailMessageFactory->create(__DIR__."/Messages/forgottenpassword.php",
                                                        ['loginJmeno'=>$loginJmeno,
                                                         'generatedPassword'=>$generatedPassword,
                                                         'data_logo_grafia' => $data_logo_grafia
                                                        ]);                    
                    $params = (new Params())
                                ->setContent(  (new Content())
                                             ->setSubject($subject)
                                             ->setHtml($body)
                                           //  ->setAttachments($attachments)
                                            )
                                ->setParty  (  (new Party())
                                            ->setFrom('noreply@najdisi.cz', 'web praci.najdisi.cz')
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
//                } else {
//                    $this->addFlashMessage("Neplatné jméno!");
//                }
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
        $loginAggregateFull = $this->statusSecurityRepo->get()->getLoginAggregate();
        if(!isset($loginAggregateFull)) {
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
//     $loginJmeno = 'Aregi10';
//     $oldHeslo = '6Nm58aqj';
//     $changeHeslo = 'Heslo10';
        if ($oldHeslo AND $changeHeslo) {
            /** @var  LoginAggregateFullInterface $loginAggregateFull  */
            $agree =  $this->authenticator->authenticate($loginAggregateFull, $oldHeslo);
            if ($agree) {
                $hashedChangedPassword = ( new Password())->getPasswordHash( $changeHeslo );
                $loginAggregateFull->getCredentials()->setPasswordHash($hashedChangedPassword );
                $this->addFlashMessage("Vaše heslo bylo změněno.");
            } else {
                $this->addFlashMessage("Nesouhlasí vaše staré heslo.");                    
            }
        }

        return $this->redirectSeeLastGet($request); // 303 See Other
    }

}
