<?php
namespace Middleware\Login\Controller;

use Site\Configuration;
use Mail\Mail;

use Mail\Params;
use Mail\Params\{Content, Attachment, Party};

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Request\RequestParams;
//use Security\Auth\AuthenticatorInterface;
use Pes\Security\Password\Password;

// model
use Model\Repository\StatusPresentationRepo;
use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusFlashRepo;
use Model\Repository\LoginAggregateRegistrationRepo;
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
class RegistrationController extends LoginControlerAbstract
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
         ResourceRegistryInterface $resourceRegistry=null,
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
            $fieldNameJmeno = Configuration::loginLogoutControler()['fieldNameJmeno'];
            $fieldNameHeslo = Configuration::loginLogoutControler()['fieldNameHeslo'];
            $fieldNameEmail = Configuration::loginLogoutControler()['fieldNameEmail'];

            $registerJmeno = $requestParams->getParsedBodyParam($request, $fieldNameJmeno, FALSE);
            $registerHeslo = $requestParams->getParsedBodyParam($request, $fieldNameHeslo, FALSE);
            $registerEmail = $requestParams->getParsedBodyParam($request, $fieldNameEmail, FALSE);

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

                    #########################--------- poslat mail -------------------
                    /** @var Mail $mail */
                    $mail = $this->container->get(Mail::class);
                    $subject =  'Veletrh práce a vzdělávání - Registrace.';
//                    $body  =
//                    "<h3> Veletrh práce a vzdělávání </h3>"
//                    ."<p>Děkujeme za Vaši registraci. <br/>Na tento mail, prosím, neodpovídejte.</p>
//                    <br/>
//                    <p> Kliknutím na níže uvedený odkaz dokončíte svoji registraci. Odkaz je aktivní následující 24 hodiny.</p>
//                    <br/><p><a href='http://localhost/web/auth/v1/confirm/$uid'>   -->> Potvrďte registraci! <<--   </a></p>"
//                    ."<br/><p>S pozdravem <br/> tým realizátora Grafia,s.r.o.</p>" ;

                    $body = $this->createMailHtmlMessage(__DIR__."/Messages/registration.php", ['confirmationUrl'=>$confirmationUrl]);

                    $attachments = [ (new Attachment())
                                    ->setFileName(Configuration::mail()['mail.files.directory'].'logo_grafia.png')  // /_www_vp_files/attachments/
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
                    // toto nefungovalo  $loginAggregateRegistrationEntity->getRegistration()->setEmailTime( new \DateTime() )

                    /**  @var Registration $registration1 */
                    $registration1 = $loginAggregateRegistrationEntity1->getRegistration();
                    $registration1->setEmailTime( new \DateTime() );
                    //$loginAggregateRegistrationEntity1->setRegistration($r); //asi není třeba

                    $this->addFlashMessage("Děkujeme za Vaši registraci. \n\n Na Vámi zadanou adresu jsme odeslali e-mail s potvrzovacím odkazem. \n\n"
                          . "Klikněte, prosím, na potvrzovací odkaz v mailové zprávě a registraci dokončete. \n (Odkaz je aktivní následující 24 hodiny.)");
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
            $fieldNameJmeno = Configuration::loginLogoutControler()['fieldNameJmeno'];
            $fieldNameHeslo = Configuration::loginLogoutControler()['fieldNameHeslo'];
            $fieldNameEmail = Configuration::loginLogoutControler()['fieldNameEmail'];

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