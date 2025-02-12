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
        $controlledItems = (new RequestParams())->getParsedBodyParam($request,'loginky');
        
//------------------------------
        //$sourceLogins = [];
//        $controlledItems = ["AndyAndy",	"Andy_/Akka/",	"CvicnyRepre",	"events_administrator",
//                         "Kralik", "navstevnik", "navstevnik1", "representative","visitor", "vlse2610" ];
//------------------------------   
        $existing=[];  
        $loginsToRemove=[]; $fullToAdd =[];
        
        if (isset($controlledItems))  {   
            //beru am sourceLogins z events, zda jsou v auth. Ty co nejsou, jsou navic v events   --- ty v events bede butno vymazat
//            foreach ($controlledItems  as $login) {
//                   /** @var LoginAggregateFullInterface $full */ 
//                $full = $this->loginAggregateFullRepo->get($login);
//                $ideName = $full->getLoginName();   //je login
//                if (isset($full)) {                                
//                   $existing[$ideName] = $full;
//                }
//                else {
//                   $loginsToRemove [$ideName] = $full;  
//                }
//            }

            
            
            // beru logins z auth, zda jsou  v kcontrolledItems. 
            // Ty co nejsou, jsou v auth navic , a budou se  pak  pridavat
            $fulls = $this->loginAggregateFullRepo->findAll();
            if ($fulls) {                                  
                         /** @var LoginAggregateFullInterface $onefull */ 
                foreach ($fulls  as $onefull) {
                    $ideName = $full->getLoginName();
                  
                    //hledam ideName v $controlledItems
                    if (in_array($ideName, $controlledItems)) {
                        $existing[$ideName] = $onefull;
                    }
                    else {
                        $fullToAdd [$ideName] = $onefull;    
                    }
                    $controlledItems = array_diff($controlledItems, [$ideName]) ; // muze byt ten samy?

                }
                    //ty, co zbydou v poli, jsou navic a budou na vymazani
                $loginsToRemove = $controlledItems; //zde je pole jen loginu
            }
            
            
            // ze vstupniho pole mazat ty co najdu - zbytek je prvni vysledne pole
            // a do druheho (vysledneho )pole patri ty, co nenajdu 
        }
        
       $result = [  'addItems' => $fullToAdd, 'remItems' => $loginsToRemove ];
        
//       json_encode($fullToAdd) 
//       json_encode($loginsToRemove)

    return $this->createJsonOKResponse( $result, 200); // 303 See Other        
    //
    //return $this->createJsonOKResponse( ["dato-Byl jsem v AUTH Synchro","Byl jsem v AUTH Synchro"], 200); // 303 See Other 
    //return $this->createStringOKResponse("Byl jsem v AUTH Synchro", 200); // 303 See Other
                                    
    }
    
}



//            try {
//                    $ret = $mail->mail($params); // posle mail
//                } catch (MailExceptionInterface $exc) {
//                    echo $exc->getTraceAsString();
//                }          