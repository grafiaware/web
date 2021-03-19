<?php

namespace Middleware\Login\Controller;

use Site\Configuration;
use Mail\Mail;

use Mail\Params;
use Mail\Params\{Content, Attachment, Party};

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Request\RequestParams;
use Pes\Security\Password\Password;

use Controller\PresentationFrontControllerAbstract;

// model
use Model\Repository\StatusPresentationRepo;
use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusFlashRepo;

use Model\Repository\LoginAggregateCredentialsRepo;
use Model\Entity\LoginAggregateCredentials;
use Model\Repository\LoginAggregateRegistrationRepo;
use Model\Entity\LoginAggregateRegistration;


/**
 * Description of passwordController
 *
 * @author vlse2610
 */
class PasswordController extends PresentationFrontControllerAbstract { 
    private $loginAggregateCredentialsRepo;
    private $loginAggregateRegistrationRepo;
    
    
    public function __construct(
                        StatusSecurityRepo $statusSecurityRepo,
                           StatusFlashRepo $statusFlashRepo,
                    StatusPresentationRepo $statusPresentationRepo,
                 ResourceRegistryInterface $resourceRegistry=null,            
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
            $fieldNameJmeno = Configuration::loginLogoutControler()['fieldNameJmeno'];            
            $loginJmeno = $requestParams->getParsedBodyParam($request, $fieldNameJmeno, FALSE);

            if ($loginJmeno ) {
                // heslo je zahashovane v Credentials - posleme mailem náhradní vygenerované, a zahashujeme do Credentials
                /** @var  LoginAggregateCredentials $loginAggregateCredentialsEntity  */
                $loginAggregateCredentialsEntity = $this->loginAggregateCredentialsRepo->get($loginJmeno);
                /** @var  LoginAggregateRegistration $loginAggregateRegistrationEntity  */
                $loginAggregateRegistrationEntity = $this->loginAggregateRegistrationRepo->get($loginJmeno);                
                
                if ( (isset ($loginAggregateCredentialsEntity)) AND (isset ($loginAggregateRegistrationEntity) ) ) {
                    $generatedPassword = $this->generatePassword();
                    $hashedGeneratedPassword = ( new Password())->getPasswordHash($generatedPassword);
                    $credentials = $loginAggregateCredentialsEntity->getCredentials()->setPasswordHash( $hashedGeneratedPassword );
                    $loginAggregateCredentialsEntity->setCredentials($credentials);
                    
                    $registerEmail = $loginAggregateRegistrationEntity->getRegistration()->getEmail();
                    
                    #########################--------- poslat mail -------------------                   
                    /** @var Mail $mail */
                    $mail = $this->container->get(Mail::class);
                    $subject =  'Heslo bylo změněno.';
                    $body  =
                    " <p>Děkujeme za zaslaný požadavek na vygenerování nového hesla.</p>
                      <p> Vaše nové přihlašovací údaje jsou:</p>
                      Jméno :  $loginJmeno     Heslo: $generatedPassword ";                     

                    $attachments = [ (new Attachment())
                                    ->setFileName(Configuration::mail()['mail.files.directory'].'logo_grafia.png')  // /_www_vp_files/attachments/
                                    ->setAltText('Logo Grafia')
                                   ];
                    $params = (new Params()) 
                                ->setContent(  (new Content())
                                             ->setSubject($subject)
                                             ->setBody($body)
                                             ->setAttachments($attachments)
                                ->setParty  (  (new Party())
                                             ->setFrom('info@najdisi.cz', 'veletrhprace.online')
                                             ->addReplyTo('svoboda@grafia.cz', 'reply veletrhprace.online')
                                             ->addTo( $registerEmail, $loginJmeno )
                                      //->addTo('selnerova@grafia.cz', 'vlse')  // ->addCc($ccAddress, $ccName)   // ->addBcc($bccAddress, $bccName)
                                            )
                                            );
                    $mail->mail($params); // posle mail
                    #########################-----------------------------
                    
                    $this->addFlashMessage("Na Váš email.adresu jsme odeslali nové přihlašovací údaje.");

                    /*nebude*/    $this->addFlashMessage("Vaše nové přihlašovací údaje jsou:\n Jméno :  $loginJmeno \n    Heslo: $generatedPassword ");
                }
                else {
                        $this->addFlashMessage("Neplatné jméno!");
                }
            }
            else {
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
        $changedPassword = $requestParams->getParsedBodyParam($request, 'changePassword', FALSE);

        if ($changedPassword ) {    
            $fieldNameJmeno = Configuration::loginLogoutControler()['fieldNameJmeno'];
            $fieldNameHeslo = Configuration::loginLogoutControler()['fieldNameHeslo'];
            $loginJmeno = $requestParams->getParsedBodyParam($request, $fieldNameJmeno, FALSE);
            $changeHeslo = $requestParams->getParsedBodyParam($request, $fieldNameHeslo, FALSE);

            $loginAggregateCredentialsEntity = $this->loginAggregateCredentialsRepo->get($loginJmeno);
            if (  (isset ($loginAggregateCredentialsEntity) )  AND $changedPassword ) {
                
                $hashedChangedPassword = ( new Password())->getPasswordHash( $changeHeslo );
                $credentials = $loginAggregateCredentialsEntity->getCredentials()->setPasswordHash( $hashedChangedPassword );
                $loginAggregateCredentialsEntity->setCredentials($credentials);
                                
                // Vaše Heslo bylo změněno.
                $this->addFlashMessage("Vaše heslo bylo změněno.");   
            }
            else {
                 $this->addFlashMessage("Neplatné jméno!");
            }
            
        }
       
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
    
}
       