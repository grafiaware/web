<?php
namespace Auth\Middleware\Login\Controler;

use Site\ConfigurationCache;

use Psr\Http\Message\ServerRequestInterface;

use FrontControler\FrontControlerAbstract;

use Pes\Http\Request\RequestParams;
//use Security\Auth\AuthenticatorInterface;
use Pes\Security\Password\Password;

// model
use Model\Repository\Exception\UnableAddEntityException;

use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;

use Auth\Model\Repository\LoginRepoInterface;
use Auth\Model\Repository\LoginAggregateFullRepoInterface;
use Auth\Model\Repository\LoginAggregateRegistrationRepoInterface;
use Auth\Model\Repository\LoginAggregateCredentialsRepoInterface;


use Status\Model\Enum\FlashSeverityEnum;



use Auth\Model\Entity\Credentials;
use Auth\Model\Entity\LoginAggregateCredentials;
use Auth\Model\Entity\Registration;
use Auth\Model\Entity\LoginAggregateRegistration;




/**
 * Description of 
 *
 * @author vlse2610
 */
class SynchroControler   extends FrontControlerAbstract {

    private $loginAggregateFullRepo;     

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            LoginAggregateFullRepoInterface $loginAggregateFullRepo        
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);       
        $this->loginRepo = $loginRepo;
        $this->loginAggregateFullRepo = $loginAggregateFullRepo;
    }

    
    
    public function synchro (ServerRequestInterface $request){
        
        $sourceLogins=[]; //toto prijde - pole loginu v events
        
        $exi=[];  $forremove=[];
        
        //prohledavam, zda jsou v auth, ty co nejsou se budou v events mazat
        foreach ($logins  as $login) {
            $full = $this->loginAggregateFullRepo->get($login);
            if (isset($full)) {
               $exi[] = $full;
            }
            else {
                $forremove[] = $full;
            }
        }
        
        $this->

        
    
    return $this->createStringOKResponse("Byl jsem v AUTH Synchro", 200); // 303 See Other
                                    
    }
    
}



   //        $requestParams = new RequestParams();
//        $companyName = $requestParams->getParsedBodyParam($request, 'companyName');
//        $loginName = $requestParams->getParsedBodyParam($request, 'loginName');   
//               
//        $loginAggregateRegistration = $this->loginAggregateRegistrationRepo->get($loginName);
//        $registerEmail = $loginAggregateRegistration->getRegistration()->getEmail();

       
//            try {
//                    $ret = $mail->mail($params); // posle mail
//                } catch (MailExceptionInterface $exc) {
//                    echo $exc->getTraceAsString();
//                }  

          


