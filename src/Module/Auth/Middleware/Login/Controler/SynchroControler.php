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

use Auth\Model\Entity\LoginInterface;

use Status\Model\Enum\FlashSeverityEnum;

use DateTime;
use Exception;


/**
 * Description of 
 *
 * @author vlse2610
 */
class SynchroControler   extends FrontControlerAbstract {

    private $loginRepo; 

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            LoginRepoInterface $loginRepo        
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);       
        $this->loginRepo = $loginRepo;
    }

                   
         
    
    /** 
     *  z routy '/auth/v1/synchro'
     * 
     * @param ServerRequestInterface $request
     * @return type
     */
    public function synchro (ServerRequestInterface $request){   
        // Obsah zavisle db prijde v request, polozky ze zavisle db se upravi podle referencni db                         
        //  remItems -  jsou v zavisle db k vymazani      
        //  addItems -  jsou k  pridavani do zavisle db         
        
        $loginsDep = $request->getParsedBody();   // pole jmen ze zavisleDB (Events)jen loginy, $controlledLogins[jmeno]=> jmeno
        $toAdd = [];  $toRem = [];
        $loginsRef = [];        
        if (isset($loginsDep))  {              
            $loginsRefDB = $this->loginRepo->findAll(); // z auth referencni db, tj. ze single_login            
            /** @var LoginInterface $oneLogin */ 
            foreach ($loginsRefDB  as $oneLogin) {  // $oneLogin je entita
                 $loginsRef[$oneLogin->getLoginName()] = $oneLogin->getLoginName();                                    
            }                                
            //LoginName ze zavisle db hleda vsechny, ktere nejsou v referencni db -> ty jsou na vymazani v zavisle, $toRem
            //LoginName ze referencni db hleda vsechny, ktere nejsou v zavisle db -> ty jsou na pridani do zavisle, $toAdd
            $toRem = array_diff($loginsDep, $loginsRef);
            $toAdd = array_diff($loginsRef, $loginsDep);                                    
  
            $result = [  'addItems' => $toAdd, 'remItems' => $toRem  ];            
        }else {
            $this->addFlashMessage("Nejsou data pro synchro-login.",  FlashSeverityEnum::WARNING);
            $result = [];
        }        
    return $this->createJsonOKResponse( $result, 200); // 303 See Other            
//    return $this->createJsonOKResponse( ["dato-Byl jsem v AUTH Synchro","Byl jsem v AUTH Synchro"], 200); // 303 See Other                                     
    }
    
             
    public function synchro_chodiciTaky (ServerRequestInterface $request){   
        // Obsah zavisle db prijde v request, polozky ze zavisle db se upravi podle referencni db                 
        //  addItems - beru postupne polozky z referencni db - do  vysledneho pole addItems  patri ty, co nenajdu v poli ze zavisle db -> jsou k  pridavani do zavisle db
        //  remItems - ze vstupniho pole ze zavisle db $controlledLogins smazu ty, co najdu v referencni db - zbytek pak je vysledne pole  remItems -> a jsou v zavisle db k vymazani                   
        
        $controlledLogins = $request->getParsedBody();   // pole jmen ze zavisleDB (Events)jen loginy, $controlledLogins[jmeno]=> jmeno
        $toAdd = [];               
        if (isset($controlledLogins))  {   
            // beru logins z auth referencni db, zjistuji zda jsou  v $controlledLogins. 
            // ty co nejsou, jsou v auth referencni navic , a budou se  pak  pridavat do zaviske
            $loginsRefDB = $this->loginRepo->findAll(); // z auth referencni db,( tj. ze single_login )
            
            /** @var LoginInterface $oneLogin */ 
            foreach ($loginsRefDB  as $oneLogin) {  // $oneLogin je entita
                $loginNameRefDB = $oneLogin->getLoginName(); // ze single_login po jednom

                //hledam $nameZAutSingle v $controlledItems=je z eventsu   
                // array_search  -  vyhledavani zastavi se na prvnim nalezenem (nam nevadi, je to klic)
                // array _diff - odstrani vsechny vyskyty, narocnejsi na pamet, pomalejsi - vzdy prohledava vsechny 
                // array_filter - 
                if (($klic = array_search($loginNameRefDB, $controlledLogins)) !== false) {  // je v zavisle
                    unset($controlledLogins[$klic]);
                }    
                else {  //neni v zavisle
                    $toAdd [$loginNameRefDB] = $loginNameRefDB;                                                  
                }                                          
            }                                                                    
            $result = [  'addItems' => $toAdd, 'remItems' => $controlledLogins  ];            
        }else {
            $this->addFlashMessage("Nejsou data pro synchro-login.",  FlashSeverityEnum::WARNING);
            $result = [];
        }        
    return $this->createJsonOKResponse( $result, 200); // 303 See Other            
//    return $this->createJsonOKResponse( ["dato-Byl jsem v AUTH Synchro","Byl jsem v AUTH Synchro"], 200); // 303 See Other                                     
    }
    
    
   
   /**/
    /** 
     *  z routy '/auth/v1/validuser'
     * 
     * @param ServerRequestInterface $request
     * @return type
     */
    public function validUser (ServerRequestInterface $request){        
        $validUserNames = $request->getParsedBody();
        $validatedUserName  = $validUserNames[0];
        
        //overit ze poslane jmeno ve $validUser[0]  je v single_login
        $login = $this->loginRepo->get($validatedUserName);    
        $name =  $login->getLoginName();
        $valid = isset($name) ? 'validUser' : 'invalidUser';                
        $result = [ 'validFromAuth' => $valid  ];          
        
        return $this->createJsonOKResponse( $result, 200); // 303 See Other            
//    return $this->createJsonOKResponse( ["dato-Byl jsem v AUTH Synchro","Byl jsem v AUTH Synchro"], 200); // 303 See Other                                     
    }
        
    
    
}