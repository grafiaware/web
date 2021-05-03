<?php
namespace Auth\Middleware\Login\Controller;

use Site\Configuration;

use Mail\Mail;
use Mail\MessageFactory\HtmlMessage;
use Mail\Params;
use Mail\Params\{Content, Attachment, Party};

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Request\RequestParams;
//use Security\Auth\AuthenticatorInterface;
use Pes\Security\Password\Password;

// model
use Model\Repository\Exception\UnableAddEntityException;

use Module\Status\Model\Repository\StatusPresentationRepo;
use Module\Status\Model\Repository\StatusSecurityRepo;
use Module\Status\Model\Repository\StatusFlashRepo;
use Auth\Model\Repository\LoginAggregateRegistrationRepo;
use Auth\Model\Repository\LoginAggregateCredentialsRepo;

use Auth\Model\Entity\Credentials;
use Auth\Model\Entity\LoginAggregateCredentials;
use Auth\Model\Entity\Registration;
use Auth\Model\Entity\LoginAggregateRegistration;
/**
 * Description of PostController
 *
 * @author pes2704
 */
class RegistrationController extends LoginControllerAbstract
{
    /**
     *
     * @var LoginAggregateRegistrationRepo
     */
    private $loginAggregateRegistrationRepo;

    /**
     *
     */
    public function __construct(
                StatusSecurityRepo $statusSecurityRepo,
                   StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
    LoginAggregateRegistrationRepo $loginAggregateRegistrationRepo )
    {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->loginAggregateRegistrationRepo = $loginAggregateRegistrationRepo;
    }

    public function register(ServerRequestInterface $request)
    {
        $requestParams = new RequestParams();
        $register = $requestParams->getParsedBodyParam($request, 'register', FALSE);

        if ($register) {
            $fieldNameJmeno = Configuration::loginLogoutController()['fieldNameJmeno'];
            $fieldNameHeslo = Configuration::loginLogoutController()['fieldNameHeslo'];

            $registerJmeno = $requestParams->getParsedBodyParam($request, $fieldNameJmeno, FALSE);
            $registerHeslo = $requestParams->getParsedBodyParam($request, $fieldNameHeslo, FALSE);
            $registerEmail = $requestParams->getParsedBodyParam($request, 'email', FALSE);
            $registerInfo = $requestParams->getParsedBodyParam($request, 'info', FALSE);

            if ($registerJmeno AND $registerHeslo AND  $registerEmail ) {
                /** @var  LoginAggregateRegistration $loginAggregateRegistrationEntity  */
                $loginAggregateRegistrationEntity = $this->loginAggregateRegistrationRepo->get($registerJmeno);

                if ( isset($loginAggregateRegistrationEntity) ) {
                    $this->addFlashMessage("Záznam se zadaným jménem jiz existuje. Zadejte jiné jméno!");
                } else {
                     // ulozit udaje do tabulky, do registration + cas: do kdy je cekano na potvrzeni registrace
                     // protoze musi byt rezervace jmena nez potvrdi v mailu
                     // zobrazit "Dekujeme za Vasi registraci. Na vas email jsme vam odeslali odkaz, kterym registraci dokoncite. Odkaz je aktivni x hodin."
                     // pak jeste jeden mail v Confirm()  "Registrace dokoncena."
                    $registration = new Registration();
                    $registration->setLoginNameFk($registerJmeno);
                    $registration->setPasswordHash( $registerHeslo );  // nezahashované
                    $registration->setEmail($registerEmail);
                    $registration->setInfo($registerInfo);

                    /** @var  LoginAggregateRegistration $loginAggregateRegistrationEntity  */
                    $loginAggregateRegistrationEntity = new LoginAggregateRegistration();
                    $loginAggregateRegistrationEntity->setLoginName($registerJmeno);
                    $loginAggregateRegistrationEntity->setRegistration($registration); // <--
                    try {
                        $this->loginAggregateRegistrationRepo->add($loginAggregateRegistrationEntity);
                    } catch (UnableAddEntityException $unableExc){
                        //zadej nové jméno.
                        $this->addFlashMessage("Záznam se zadaným jménem již existuje. Zadejte jiné jméno!");
                    }
                    //---------------------------------------------
                    $this->loginAggregateRegistrationRepo->flush();
                    //---------------------------------------------

                    /** @var  LoginAggregateRegistration $loginAggregateRegistrationEntity1  */
                    $loginAggregateRegistrationEntity1 = $this->loginAggregateRegistrationRepo->get($registerJmeno);
                    $uid = $loginAggregateRegistrationEntity1->getRegistration()->getUid();    //do mailu potrebuji vygenerovane uid z tabulky registration

                    $confirmDomainName = $request->getServerParams()['HTTP_HOST'].$this->getBasePath($request);  // getBasePath - končí /
                    $confirmationUrl = "http://{$confirmDomainName}auth/v1/confirm/$uid";

                    #########################--------- poslat mail uzivateli-------------------
                    /** @var Mail $mail */
                    $mail = $this->container->get(Mail::class);
                    /** @var HtmlMessage $mailMessageFactory */
                    $mailMessageFactory = $this->container->get(HtmlMessage::class);

                    $subject =  'Veletrh práce a vzdělávání - Registrace.';
                    $body = $mailMessageFactory->create(__DIR__."/Messages/registration.php", ['confirmationUrl'=>$confirmationUrl ]);

                    $attachments = [ (new Attachment())
                                    ->setFileName(Configuration::mail()['mail.attachments'].'logo_grafia.png')
                                    ->setAltText('Logo Grafia')
                                   ];
                    $params = (new Params())
                                ->setContent(  (new Content())
                                                 ->setSubject($subject)
                                                 ->setHtml($body)
//                                                 ->setAttachments($attachments)
                                            )
                                ->setParty  (  (new Party())
                                                 ->setFrom('it.grafia@gmail.com', 'veletrhprace.online')
                                                 ->addReplyTo('svoboda@grafia.cz', 'reply veletrhprace.online')
                                                 ->addTo( $registerEmail, $registerJmeno)
                                                  //->addTo('selnerova@grafia.cz', 'vlse')  // ->addCc($ccAddress, $ccName)   // ->addBcc($bccAddress, $bccName)
                                            );
                    $mail->mail($params); // posle mail
                    #########################-----------------------------

                    //zapsat cas mailu do registration
                    $loginAggregateRegistrationEntity1->getRegistration()->setEmailTime( new \DateTime() );

                    $this->addFlashMessage("Děkujeme za Vaši registraci. \n Na Vaši adresu jsme odeslali potvrzovací mail.\n"
                          . "V mailu registraci dokončete.");

                    #########################---------  kopie -------------------
                    /** @var Mail $mail */
                    $mail = $this->container->get(Mail::class);
                    /** @var HtmlMessage $mailMessageFactory */
                    $mailMessageFactory = $this->container->get(HtmlMessage::class);

                    $subject =  "veletrhprace.online - Kopie zaslaného mailu - Registrace: '$registerJmeno'";
                    $body = $mailMessageFactory->create(__DIR__."/Messages/registration.php", ['confirmationUrl'=>$confirmationUrl ]);

                    $attachments = [ (new Attachment())
                                    ->setFileName(Configuration::mail()['mail.attachments'].'logo_grafia.png')
                                    ->setAltText('Logo Grafia')
                                   ];
                    $params = (new Params())
                                ->setContent(  (new Content())
                                                 ->setSubject($subject)
                                                 ->setHtml($body)
//                                                 ->setAttachments($attachments)
                                            )
                                ->setParty  (  (new Party())
                                                 ->setFrom('it.grafia@gmail.com', 'veletrhprace.online')
                                                 ->addTo('svoboda@grafia.cz', 'copy registration veletrhprace.online')
                                                  //->addTo('selnerova@grafia.cz', 'vlse')  // ->addCc($ccAddress, $ccName)   // ->addBcc($bccAddress, $bccName)
                                            );
                    $mail->mail($params); // posle mail
                    #########################-----------------------------

                    if ($registerInfo) {
                        //zde zapsat do databaze - KAM??

                        ##########################--------- poslat mail do Grafie - registrujici se  je  vystavovatel -------------------
                        /** @var Mail $mail */
                        $mail = $this->container->get(Mail::class);
                        /** @var HtmlMessage $mailMessageFactory */
                        $mailMessageFactory = $this->container->get(HtmlMessage::class);

                        $subject =  'Veletrh práce a vzdělávání - Registrace zástupce vystavovatele.';
                        $body = $mailMessageFactory->create(__DIR__."/Messages/registrationexhib.php",
                                                            ['registerJmeno' => $registerJmeno,
                                                             'registerHeslo' => $registerHeslo,
                                                             'registerEmail' => $registerEmail,
                                                             'registerInfo' => $registerInfo,
                                                            ]);
                        $attachments = [ (new Attachment())
                                        ->setFileName(Configuration::mail()['mail.attachments'].'logo_grafia.png')
                                        ->setAltText('Logo Grafia')
                                       ];
                        $params = (new Params())
                                    ->setContent(  (new Content())
                                                     ->setSubject($subject)
                                                     ->setHtml($body)
    //                                                 ->setAttachments($attachments)
                                                )
                                    ->setParty  (  (new Party())
                                                     ->setFrom('it.grafia@gmail.com', 'veletrhprace.online')
                                                     ->addTo('svoboda@grafia.cz', 'Registace vystavovatele veletrhprace.online')
                                                     ->addTo('pirnosova@grafia.cz', 'Registace vystavovatele veletrhprace.online')
                                                );
                        $mail->mail($params); // posle mail
                        #########################-----------------------------
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
            $fieldNameJmeno = Configuration::loginLogoutController()['fieldNameJmeno'];
            $fieldNameHeslo = Configuration::loginLogoutController()['fieldNameHeslo'];
            $fieldNameEmail = Configuration::loginLogoutController()['fieldNameEmail'];

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
}