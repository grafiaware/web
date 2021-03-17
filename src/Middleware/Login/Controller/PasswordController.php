<?php

namespace Middleware\Login\Controller;

use Site\Configuration;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Request\RequestParams;
use Security\Auth\AuthenticatorInterface;
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
                    $generatedPassword = 'bflm';
                    $hashedGeneratedPassword = ( new Password())->getPasswordHash($generatedPassword);
                    $credentials = $loginAggregateCredentialsEntity->getCredentials()->setPasswordHash( $hashedGeneratedPassword);
                    $loginAggregateCredentialsEntity->setCredentials($credentials);

                    //**mail**
                    // Děkujeme za zaslaný požadavek na vygenerování nového hesla.
                    // Vaše nové přihlašovací údaje jsou:
                    // Jméno :  $loginJmeno     Heslo: $generatedPassword
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
                $loginAggregateCredentialsEntity
            }
            
        }
       
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
    
}
       