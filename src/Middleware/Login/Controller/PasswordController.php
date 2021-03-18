<?php

namespace Middleware\Login\Controller;

use Site\Configuration;
use Mail\Mail;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Request\RequestParams;
use Pes\Security\Password\Password;

use Controller\StatusFrontControllerAbstract;

// model
use Model\Repository\StatusPresentationRepo;
use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusFlashRepo;

use Model\Repository\LoginAggregateCredentialsRepo;
use Model\Entity\LoginAggregateCredentials;


/**
 * Description of passwordController
 *
 * @author vlse2610
 */
class PasswordController extends StatusFrontControllerAbstract { 
    private $loginAggregateCredentialsRepo;
    
    
    public function __construct(
                        StatusSecurityRepo $statusSecurityRepo,
                           StatusFlashRepo $statusFlashRepo,
                    StatusPresentationRepo $statusPresentationRepo,
            
             LoginAggregateCredentialsRepo $loginAggregateCredentialRepo
            ) {
        
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->loginAggregateCredentialsRepo = $loginAggregateCredentialRepo;
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
                
                if (isset ($loginAggregateCredentialsEntity) ) {
                    $generatedPassword = $this->generatePassword();
                    $hashedGeneratedPassword = ( new Password())->getPasswordHash($generatedPassword);
                    $credentials = $loginAggregateCredentialsEntity->getCredentials()->setPasswordHash( $hashedGeneratedPassword );
                    $loginAggregateCredentialsEntity->setCredentials($credentials);

                    //**mail**
                    // Děkujeme za zaslaný požadavek na vygenerování nového hesla.
                    // Vaše nové přihlašovací údaje jsou:
                    // Jméno :  $loginJmeno     Heslo: $generatedPassword          
                    $mail = new Mail();
                    $mail->mail('body_forgottenPassword');

                    
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
       