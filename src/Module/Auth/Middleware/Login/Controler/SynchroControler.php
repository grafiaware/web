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

use DateTime;
use Exception;


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
        // obsah zavisle db prijde v request, a ten se upravuje podle referencmi db 
                
        //  addItems - beru postupne polozky z referencmi db - do  vysledneho pole addItems  patri ty, co nenajdu v poli ze zavisle db - jsou k  pridavani do zavisle db
        //  remItems - ze vstupniho pole ze zavisle db $controlledItems smazanu ty, co najdu v referencni db - zbytek pak je vysledne pole  remItems -> a jsou v zavisle db k vymazani            
        
        $controlledItems = $request->getParsedBody();     
       // $controlledItems  pole, jen loginy,  pole ze zavisle db
        $existing=[];  
        $fullToAdd =[];
        
        if (isset($controlledItems))  {   
            // beru logins z auth, zda jsou  v controlledItems. 
            // ty co nejsou, jsou v auth navic , a budou se  pak  pridavat
            $fulls = $this->loginAggregateFullRepo->findAll();
            
            if ($fulls) {                                  
                         /** @var LoginAggregateFullInterface $onefull */ 
                foreach ($fulls  as $onefull) {
                    $ideName = $onefull->getLoginName();
                  
                    //hledam ideName v $controlledItems
                    if (in_array($ideName, $controlledItems)) {
                        $existing[$ideName] = $onefull;                        
                        $reArr = array_diff($controlledItems, [$ideName] ) ;
                        $controlledItems = $reArr;
//                        $controlledItems = array_diff($controlledItems, [0 =>$ideName] ) ; // muze byt ten samy? spis ne
                    }
                    else {
                        $fullToAdd [$ideName]['modul'] = 'auth';
                        if ( ( null !== $onefull->getCredentials() ) ) {
                            $fullToAdd [$ideName]['role'] = $onefull->getCredentials()->getRoleFk();
                        } else {
                            $fullToAdd [$ideName]['role'] = "";
                        } 
                        if ( ( null !== $onefull->getRegistration() )) {
                            $fullToAdd [$ideName]['email'] = $onefull->getRegistration()->getEmail();
                            $fullToAdd [$ideName]['info'] = $onefull->getRegistration()->getInfo();
                        }else {
                            $fullToAdd [$ideName]['email'] = "";
                            $fullToAdd [$ideName]['info'] = "";
                        }                                               
                    }
                }
                
            }          
        
            $result = [  'addItems' => $fullToAdd, 'remItems' => $controlledItems ];
            
        }else {
            $this->addFlashMessage("Nejsou data pro synchro-login.",  FlashSeverityEnum::WARNING);
            $result = [];
        }        

    return $this->createJsonOKResponse( $result, 200); // 303 See Other            
//    return $this->createJsonOKResponse( ["dato-Byl jsem v AUTH Synchro","Byl jsem v AUTH Synchro"], 200); // 303 See Other                                     
    }
    
}
        
//------------------------------ 
//        $controlledItems = ["AndyAndy",	"Andy_/Akka/",	"CvicnyRepre",	"events_administrator",
//                         "Kralik", "navstevnik", "navstevnik1", "representative","visitor", "vlse2610" ];
//------------------------------ 