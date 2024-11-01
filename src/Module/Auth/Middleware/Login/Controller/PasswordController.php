<?php

namespace Auth\Middleware\Login\Controller;

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

use Red\Model\Repository\LoginAggregateCredentialsRepo;
use Red\Model\Entity\LoginAggregateCredentials;
use Red\Model\Repository\LoginAggregateRegistrationRepo;
use Red\Model\Entity\LoginAggregateRegistration;


/**
 * Description of passwordController
 *
 * @author vlse2610
 */
class PasswordController extends LoginControllerAbstract {
    private $loginAggregateCredentialsRepo;
    private $loginAggregateRegistrationRepo;


    public function __construct(
                        StatusSecurityRepo $statusSecurityRepo,
                           StatusFlashRepo $statusFlashRepo,
                    StatusPresentationRepo $statusPresentationRepo,
             LoginAggregateCredentialsRepo $loginAggregateCredentialRepo,
            LoginAggregateRegistrationRepo $loginAggregateRegistrationRepo)
    {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->loginAggregateCredentialsRepo = $loginAggregateCredentialRepo;
        $this->loginAggregateRegistrationRepo = $loginAggregateRegistrationRepo;
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
                $loginAggregateCredentialsEntity = $this->loginAggregateCredentialsRepo->get($loginJmeno);

                if ( isset($loginAggregateCredentialsEntity)  AND isset($loginAggregateRegistrationEntity) )
                {
                    $credentials = $loginAggregateCredentialsEntity->getCredentials();
                    $registration = $loginAggregateRegistrationEntity->getRegistration();
                    if  (isset($credentials) AND isset($registration))
                    {
                        $generatedPassword = $this->generatePassword();
                        $hashedGeneratedPassword = ( new Password())->getPasswordHash($generatedPassword);

                        $credentials->setPasswordHash( $hashedGeneratedPassword );
//                        $loginAggregateCredentialsEntity->setCredentials($credentials);

                        $registerEmail = $registration->getEmail();

                        #########################--------- poslat mail -------------------
                        /** @var Mail $mail */
                        $mail = $this->container->get(Mail::class);
                        /** @var HtmlMessage $mailMessageFactory */
                        $mailMessageFactory = $this->container->get(HtmlMessage::class);

                        $subject =  'Veletrh práce a vzdělávání - Nové heslo.';
                        $body = $mailMessageFactory->create(__DIR__."/Messages/forgottenpassword.php",
                                                            ['loginJmeno'=>$loginJmeno,
                                                             'generatedPassword'=>$generatedPassword
                                                            ]);

                        $attachments = [ (new Attachment())
                                        ->setFileName(ConfigurationCache::mail()['mail.attachments'].'logo_grafia.png')
                                        ->setAltText('Logo Grafia')
                                       ];
                        $params = (new Params())
                                    ->setContent(  (new Content())
                                                 ->setSubject($subject)
                                                 ->setHtml($body)
              //                                   ->setAttachments($attachments)
                                                )
                                    ->setParty  (  (new Party())
                                                 ->setFrom('info@najdisi.cz', 'veletrhprace.online')
                                                 ->addReplyTo('svoboda@grafia.cz', 'reply veletrhprace.online')
                                                 ->addTo( $registerEmail, $loginJmeno )
                                          //->addTo('selnerova@grafia.cz', 'vlse')  // ->addCc($ccAddress, $ccName)   // ->addBcc($bccAddress, $bccName)
                                                );
                        $mail->mail($params); // posle mail
                        #########################-----------------------------

                        $this->addFlashMessage("Na Vaši email.adresu jsme odeslali nové přihlašovací údaje.");
                    } else {
                        $this->addFlashMessage("Váš účet nebyl založen registrací, neznáme Vaši email adresu. Nelze vám zaslat nové přihlašovací údaje.");
                    }
                } else {
                    $this->addFlashMessage("Neplatné jméno!");
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
        $requestParams = new RequestParams();
        $changedPassword = $requestParams->getParsedBodyParam($request, 'changepassword', FALSE);

        if ($changedPassword ) {
            $fieldNameJmeno = ConfigurationCache::auth()['fieldNameJmeno'];
            $fieldNameHeslo = ConfigurationCache::auth()['fieldNameHeslo'];
            $loginJmeno = $requestParams->getParsedBodyParam($request, $fieldNameJmeno, FALSE);
            $changeHeslo = $requestParams->getParsedBodyParam($request, $fieldNameHeslo, FALSE);

            /** @var  LoginAggregateCredentials $loginAggregateCredentialsEntity  */
            $loginAggregateCredentialsEntity = $this->loginAggregateCredentialsRepo->get($loginJmeno);
            if  ($loginAggregateCredentialsEntity !== \NULL  )
            {
                if (  ($loginAggregateCredentialsEntity->getCredentials() !== \NULL )   AND $changeHeslo ) {
                    $hashedChangedPassword = ( new Password())->getPasswordHash( $changeHeslo );
                    $credentials = $loginAggregateCredentialsEntity->getCredentials()->setPasswordHash( $hashedChangedPassword );
                    $loginAggregateCredentialsEntity->setCredentials($credentials);

                    $this->addFlashMessage("Vaše heslo bylo změněno.");
                }
            }
            else {
                 $this->addFlashMessage("Neplatné jméno!");
            }

        }

        return $this->redirectSeeLastGet($request); // 303 See Other
    }

}
