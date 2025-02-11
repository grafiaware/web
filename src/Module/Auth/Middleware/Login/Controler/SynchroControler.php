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


use Auth\Model\Entity\LoginAggregateFullInterface;

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
        
        
        $sourceLoginsVEvents = (new RequestParams())->getParsedBodyParam($request,'logins');
        
//------------------------------
        //$sourceLogins = [];
        $sourceLoginsVEvents = ["AndyAndy",	"Andy_/Akka/",	"CvicnyRepre",	"events_administrator",
                         "Kralik", "navstevnik", "navstevnik1", "representative","visitor", "vlse2610" ];
//------------------------------   
        
        $existing=[];  $jsouNavicVEvents=[]; $nejsouVEvents =[];
        
        if (isset($sourceLoginsVEvents))  {   
            //beru am sourceLogins z events, zda jsou v auth. Ty co nejsou, jsou navic v events
            foreach ($sourceLoginsVEvents  as $login) {
                   /** @var LoginAggregateFullInterface $full */ 
                $full = $this->loginAggregateFullRepo->get($login);
                $ideName = $full->getLoginName();   //je login
                if (isset($full)) {                                
                   $existing[$ideName] = $full;
                }
                else {
                   $jsouNavicVEvents [$ideName] = $full;  
                }
            }

            // beru logins z auth, zda jsou  v events, ty co nejsou, jsou navic v auth, tj.nejsou v events
            $fulls = $this->loginAggregateFullRepo->findAll();
            if ($fulls) {                                  
                         /** @var LoginAggregateFullInterface $onefull */ 
                foreach ($fulls  as $onefull) {
                    $ideName = $onefull->getLoginName();
                    //hledam ideName v $sourceLogins
                    if (in_array($ideName, $sourceLoginsVEvents)) {
                         $existing[$ideName] = $onefull;
                    }
                    else {
                        $nejsouVEvents [$ideName] = $onefull;    
                    }
                }

            }
            
            
            //ze vstupniho pole mayat tz co najdu
            //a  do druheho (vysledneho )pole patri  tz co nenajdu - ale budou v poli nakem yvlast
        }
        
        
        
        
//        $this->

        
    return $this->createJsonOKResponse( ["dato-Byl jsem v AUTH Synchro","Byl jsem v AUTH Synchro"], 200); // 303 See Other 
    //return $this->createStringOKResponse("Byl jsem v AUTH Synchro", 200); // 303 See Other
                                    
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

          


