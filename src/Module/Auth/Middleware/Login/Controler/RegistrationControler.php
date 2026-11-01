<?php
namespace Auth\Middleware\Login\Controler;

use Site\ConfigurationCache;
use Mail\MailSettings;

use Pes\Mail\MailInterface;
use Mail\MessageFactory\HtmlMessage;
use Pes\Mail\Assembly;
use Pes\Mail\Assembly\{Content, Attachment, Party};
use Pes\Mail\Exception\MailExceptionInterface;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Request\RequestParams;
//use Security\Auth\AuthenticatorInterface;
use Pes\Core\Security\Password\Password;

// model
use Pes\Model\Repository\Exception\UnableAddEntityException;

use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Auth\Model\Repository\LoginAggregateRegistrationRepo;
use Auth\Model\Repository\LoginAggregateCredentialsRepo;

use Auth\Model\Entity\Credentials;
use Auth\Model\Entity\LoginAggregateCredentials;
use Auth\Model\Entity\Registration;
use Auth\Model\Entity\LoginAggregateRegistration;

/**
 * Description of PostControler
 *
 * @author pes2704
 */
class RegistrationControler extends LoginControlerAbstract
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
            $fieldNameJmeno = ConfigurationCache::auth()['fieldNameJmeno'];
            $fieldNameHeslo = ConfigurationCache::auth()['fieldNameHeslo'];

            $registerJmeno = $requestParams->getParsedBodyParam($request, $fieldNameJmeno, FALSE);
            $registerHeslo = $requestParams->getParsedBodyParam($request, $fieldNameHeslo, FALSE);
            $registerEmail = $requestParams->getParsedBodyParam($request, 'email', FALSE);
            $registerInfo  = $requestParams->getParsedBodyParam($request, 'info', FALSE);

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
                    $registration->setPasswordHash( $registerHeslo );  // nezahashované, hashuje se po konfirmaci
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
                    $confirmationUrl = "https://{$confirmDomainName}auth/v1/confirm/$uid";

                    #########################--------- poslat mail uzivateli---------------xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
                    /** @var MailInterface $mail */
                    $mail = $this->container->get(MailInterface::class);
                    /** @var HtmlMessage $mailMessageFactory */
                    $mailMessageFactory = $this->container->get(HtmlMessage::class);                    
                    
                    $attachments = [ (new Attachment())
//                                    ->setFileName(ConfigurationCache::mail()['mail.attachments'].'logo_grafia.png')
//                                    ->setAltText('Logo Grafia')
                                   ];                   
                    $mailSettings = MailSettings::fromConfiguration();
                    $data_logo_grafia = $mailSettings->logoDataUri('grafia');
                    //base64_encode()
                    $subject =  'Veletrh práce a vzdělávání - Registrace.';
                    $body = $mailMessageFactory->create(__DIR__."/Messages/registration.php",  ['confirmationUrl'  => $confirmationUrl,
                                                                                                'data_logo_grafia' => $data_logo_grafia,
                                                                                              //  'logo_grafia'   => 'logo_grafia.png'
                                                                               //  'attachments'=>$attachments   //takto pouze jako příloha
                                                                                                ]);
                    $params = (new Assembly())
                                ->setContent(  (new Content())
                                                 ->setSubject($subject)
                                                 ->setHtml($body)
                                                //->setAttachments($attachments)  //priloha
                                            )
                                ->setParty  (  $mailSettings->addRecipients(
                                                 $mailSettings->party('info')->addTo( $registerEmail, $registerJmeno),
                                                 'bcc', 'registration.bcc')
                                            );
                    $mail->mail($params); // posle mail
                    #########################-----------------------------

                    //zapsat cas mailu do registration
                    $loginAggregateRegistrationEntity1->getRegistration()->setEmailTime( new \DateTime() );

                    $this->addFlashMessage("Děkujeme za Vaši registraci. \n Na Vaši adresu jsme odeslali potvrzovací mail.\n"
                          . "V mailu registraci dokončete.");

                    
                    
                    
                    
//                    #########################---------  kopie -------------------xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
//                    /** @var MailInterface $mail */
//                    $mail = $this->container->get(MailInterface::class);
//                    /** @var HtmlMessage $mailMessageFactory */
//                    $mailMessageFactory = $this->container->get(HtmlMessage::class);
//                    
//                    $attachments = [ (new Attachment())
//                                  //  ->setFileName(ConfigurationCache::mail()['mail.attachments'].'logo_grafia.png')
//                                 //   ->setAltText('Logo Grafia')
//                                   ];
//
//                    $subject =  "praci.najdisi.cz - Kopie zaslaného mailu - Registrace: '$registerJmeno'";
//                    $body = $mailMessageFactory->create(__DIR__."/Messages/registration.php", 
//                                                        ['confirmationUrl'=>$confirmationUrl,
//                                                        // 'attachments'=>$attachments    // zde zbytecne
//                                                        ]);                   
//                    $params = (new Params())
//                                ->setContent(  (new Content())
//                                                 ->setSubject($subject)
//                                                 ->setHtml($body)
////                                                 ->setAttachments($attachments)
//                                            )
//                                ->setParty  (  (new Party())
//                                                  ->setFrom('noreply@najdisi.cz', 'web najisi')
//                                                  //->addReplyTo('allmail@grafia.cz', 'info web praci.najdisi.cz')   //nejak neumime
//                                                  //->addReplyTo('selnerova@grafia.cz', 'info web praci.najdisi.cz') //nejak neumime
//                                                  ->addTo('it@grafia.cz', 'kopie pro Grafii')
//                                                  // ->addCc($ccAddress, $ccName)   // ->addBcc($bccAddress, $bccName)
//                                            );
//                    $mail->mail($params); // posle mail
//                   #########################--------------xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
                  
                    
//                    if ($registerInfo) {
//                        ##########################--------- poslat mail do Grafie - registrujicise  je  vystavovatel -------------------
//                        /** @var MailInterface $mail */
//                        $mail = $this->container->get(MailInterface::class);
//                        /** @var HtmlMessage $mailMessageFactory */
//                        $mailMessageFactory = $this->container->get(HtmlMessage::class);
//
//                        $subject =  'Veletrh práce a vzdělávání - Registrace zástupce vystavovatele.';
//                        $body = $mailMessageFactory->create(__DIR__."/Messages/registrationexhib.php",
//                                                            ['registerJmeno' => $registerJmeno,
//                                                             'registerHeslo' => $registerHeslo,
//                                                             'registerEmail' => $registerEmail,
//                                                             'registerInfo' => $registerInfo,
//                                                            ]);
//                        $attachments = [ (new Attachment())
////                                        ->setFileName(ConfigurationCache::mail()['mail.attachments'].'logo_grafia.png')
////                                        ->setAltText('Logo Grafia')
//                                       ];
//                        $params = (new Params())
//                                    ->setContent(  (new Content())
//                                                     ->setSubject($subject)
//                                                     ->setHtml($body)
//    //                                                 ->setAttachments($attachments)   //zde zbytecne
//                                                )
//                                    ->setParty  (  (new Party())
//                                                     ->setFrom('noreply@najdisi.cz', 'web praci.najdisi.cz')
//                                                    // ->addReplyTo('allmail@grafia.cz', 'info web praci.najdisi.cz')
//                                                    // ->addTo('svoboda@grafia.cz', 'Registace zástupce vystavovatele')
//                                                     ->addTo('it@grafia.cz', 'Registace zástupce vystavovatele')
//                                                );
//                        $mail->mail($params); // posle mail
//                        #########################-----------------------------
//                    }

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
            $fieldNameJmeno = ConfigurationCache::auth()['fieldNameJmeno'];
            $fieldNameHeslo = ConfigurationCache::auth()['fieldNameHeslo'];
            $fieldNameEmail = ConfigurationCache::auth()['fieldNameEmail'];

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
    
    
    
    
    public function sendMailCompletRegistrationRepre(ServerRequestInterface $request) {
        $requestParams = new RequestParams();
        $companyName = $requestParams->getParsedBodyParam($request, 'companyName');
        $loginName = $requestParams->getParsedBodyParam($request, 'loginName');   
               
        $loginAggregateRegistration = $this->loginAggregateRegistrationRepo->get($loginName);
        $registerEmail = $loginAggregateRegistration->getRegistration()->getEmail();

        if ($companyName and $loginName) {
        // #########################--------- poslat mail -------------------
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
                $subject =  'Veletrh práce a vzdělávání - Registrace dokončena.';

                $body = $mailMessageFactory->create(__DIR__."/Messages/confirmRepre2.php",
                                                    ['data_logo_grafia' => $data_logo_grafia,
                                                     'companyName' => $companyName,
                                                      // 'logo_grafia'   => 'logo_grafia.png'
                                                    ] );                                                
                $params = (new Assembly())
                            ->setContent(  (new Content())
                                         ->setSubject($subject)
                                         ->setHtml($body)
//                                              ->setAttachments($attachments)
                                         )
                            ->setParty  (  $mailSettings->party('info')
                                         ->addTo( $registerEmail, $loginName)
                                        );
            try {
                    $ret = $mail->mail($params); // posle mail
                } catch (MailExceptionInterface $exc) {
                    throw $exc;
                }  

        }   
        $this->addFlashMessage("Mail odeslán");
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
    
    
    
    
    
    public function testMail(ServerRequestInterface $request) {
        
        $requestParams = new RequestParams();
        $value1 = $requestParams->getParsedBodyParam($request, 'key1');
        $value2 = $requestParams->getParsedBodyParam($request, 'key2');
        
        $requestBody = $request->getParsedBody();
        
                    /** @var MailInterface $mail */
                    $mail = $this->container->get(MailInterface::class);
                    /** @var HtmlMessage $mailMessageFactory */
                    $mailMessageFactory = $this->container->get(HtmlMessage::class);

                    $subject =  'Veletrh práce a vzdělávání - TEST mail.';
                    $mailSettings = MailSettings::fromConfiguration();
                    $data_logo_grafia = $mailSettings->logoDataUri('grafia');
                    //base64_encode()
                    $body = $mailMessageFactory->create(__DIR__."/Messages/testmail.php", 
                            [
                                'confirmationUrl'  => "testovaci_url",
                                'requestBody'=>$requestBody, 'value1'=>$value1, 'value2'=>$value2,
                                'data_logo_grafia' => $data_logo_grafia,
                            ]);
                    
                    $params = (new Assembly())
                                ->setContent(  (new Content())
                                                 ->setSubject($subject)
                                                 ->setHtml($body)
                                            )
                                ->setParty  (  $mailSettings->addRecipients($mailSettings->party('testmail'), 'to', 'testmail.to')
                                            );
                    try {
                        $ret = $mail->mail($params); // posle mail
                    } catch (MailExceptionInterface $exc) {
                        throw $exc;
                    }

        $this->addFlashMessage("Mail odeslán");
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
}
