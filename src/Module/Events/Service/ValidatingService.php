<?php
<?php
namespace Events\Service;

use Events\Service\ValidatingServiceInterface;

use Events\Service\ValidatingException;

use Pes\Application\UriInfoInterface;
use Psr\Http\Message\ServerRequestInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Entity\Security;
use Status\Model\Entity\SecurityInterface;
use Pes\Logger\FileLogger;

use Events\Model\Entity\LoginInterface;
use Events\Model\Entity\Login;
use Access\Enum\RoleEnum;

use Pes\Application\AppFactory;
use LogicException;


use Status\Model\Enum\FlashSeverityEnum;


/**
 * Description of Validate
 *
 * @author vlse2610
 */
class ValidatingService implements ValidatingServiceInterface {              
    private $statusSecurityRepo;
    private $statusFlashRepo;
    private $statusPresentationRepo;
    //private $loginRepo;
    private $loginService;    
    private $fileLogger;
   
     
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,               
            LoginServiceInterface $loginService,
            
            ?FileLogger $fileLogger = null
            ) {        
            
        $this->statusSecurityRepo = $statusSecurityRepo;
        $this->statusFlashRepo = $statusFlashRepo;
        $this->statusPresentationRepo = $statusPresentationRepo;                    
        $this->loginService = $loginService;
   
        $this->fileLogger = $fileLogger;        
    } 
    
 
    
      
    #[\Override]   
    public function validateUser (ServerRequestInterface $request): void {                   
             /** @var SecurityInterface $security */
        $security = $this->statusSecurityRepo->get();  //
        if (isset($security)) {
            $loginAgregate = $security->getLoginAggregate();                 
            $validatedUserName = $loginAgregate?->getLoginName();
            
            if (isset($validatedUserName))  {   
                //$validatedUserName="Xxx";
                try {
                    //validovat                    
                    $this->validateUserByAuthServer($request, $validatedUserName);
                    $this->loginService->setAddUserNameToEventsLogin($validatedUserName);
//                    $this->fileLogger?->notice("* " . $validatedUserName . " je validnÃ­ uÅ¾ivatel( je v single_login)," .
//                                       ' pokud nebyl v tabulce events.login. -> tak tam byl pÅidÃ¡n.' ); 
                    //nenÃ­-li ve statusu security (prvni request, jeste nezvalidovano), poznamenat do statusu security 
                    if (!$security->isUserNameVerifiedWithinSession($validatedUserName)) {
                        $security->addUserNameVerifiedWithinSession($validatedUserName);
                    }                    
                }
                catch (ValidatingException $e) {
                    $security->removeContext();   
                    $this->loginService->setDeleteUserNameFromEventsLogin($validatedUserName);
//                    $this->fileLogger?->notice("* " . $validatedUserName . " nenÃ­ validnÃ­ uÅ¾ivatel (nenÃ­ v single_login)." .
//                                                       " - a byl 'vymazÃ¡n' z tabulky events.login (byl-li tam)" );                         
                    $this->fileLogger?->error($e->getMessage());  
                }  
                catch (ConnectionException $e) {
                    $security->removeContext();   
                    $this->fileLogger?->error($e->getMessage());  
                }
            }
            else{   //neni  $validatedUserName                  
                $this->fileLogger?->notice("NevalidovÃ¡no - Nikdo nenÃ­ pÅihlaÅ¡enÃ½." );                                
            }   
        }                
    }
                          
    
  
    /**
     * 
     * @param ServerRequestInterface $request
     * @param SecurityInterface $security
     * @param string $validatedUserName
     * @return void
     */
    private function validateUserByAuthServer( ServerRequestInterface $request, string $validatedUserName ):void {                               

        $scheme = $request->getUri()->getScheme();
        $host = $request->getUri()->getHost();
        $ruri = $this->getUriInfo($request)->getRestUri();
        $rap =$this->getUriInfo($request)->getRootAbsolutePath();
        $sp = $this->getUriInfo($request)->getSubdomainPath();        
        $url = "$scheme://$host$sp"."auth/v1/validuser";
        // options pro stream_context_create() vÅ¾dy definuj s poloÅ¾kou http
        // url adresu pro file_get_contents(url, ..) definuj: https://....
        // use key 'http' even if you send the request to https://...
        $json = json_encode([$validatedUserName]);
        $options = [
            'http' => [
                'header' => "Content-type: application/json",
                //'header' => "Cookie: XDEBUG_SESSION=netbeans-xdebug\r\n",
                'method' => 'POST' ,
                'content' => $json,
            ],
        ];        
        //--------------      
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);   //posle  na url, vysledek z auth ...content pak priradi do result      
        //--------------        
        //** zkouska    $result=false;
        if ($result!==false) {
            $resultData = json_decode($result, true);            
            if ($resultData ['validFromAuth'] == 'invalidUser')  {
                throw new ValidatingException("validateUserByAuthServer: UÅ¾ivatel nenÃ­ validnÃ­.");              
            }        
        } else {                                                      
            throw new ConnectionException("validateUserByAuthServer: Spojeni se nezdaÅilo. Nelze validovat(ovÄÅit) uÅ¾ivatele.");            
        }                               
    }
    
            
    
    
     /**
     * PomocnÃ¡ metoda - zÃ­skÃ¡ base path z objektu UriInfo, kterÃ½ byl vloÅ¾en do requestu jako atribut s jmÃ©nem AppFactory::URI_INFO_ATTRIBUTE_NAME v AppFactory.
     *
     * @return UriInfoInterface
     */
     private function getUriInfo(ServerRequestInterface $request): UriInfoInterface {
        $uriInfo = $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME);
        if (! $uriInfo instanceof UriInfoInterface) {
            throw new LogicException("Atribut requestu ".AppFactory::URI_INFO_ATTRIBUTE_NAME." neobsahuje objekt typu ".UriInfoInterface::class.".");
        }
        return $uriInfo;
    }
    
     
}
namespace Events\Service;

use Events\Service\ValidatingServiceInterface;

use Events\Service\ValidatingException;

use Pes\Application\UriInfoInterface;
use Psr\Http\Message\ServerRequestInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Entity\Security;
use Status\Model\Entity\SecurityInterface;
use Pes\Logger\FileLogger;

use Events\Model\Entity\LoginInterface;
use Events\Model\Entity\Login;
use Access\Enum\RoleEnum;

use Pes\Application\AppFactory;
use LogicException;


use Status\Model\Enum\FlashSeverityEnum;


/**
 * Description of Validate
 *
 * @author vlse2610
 */
class ValidatingService implements ValidatingServiceInterface {              
    private $statusSecurityRepo;
    private $statusFlashRepo;
    private $statusPresentationRepo;
    //private $loginRepo;
    private $loginService;    
    private $fileLogger;
   
     
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,               
            LoginServiceInterface $loginService,
            
            ?FileLogger $fileLogger = null
            ) {        
            
        $this->statusSecurityRepo = $statusSecurityRepo;
        $this->statusFlashRepo = $statusFlashRepo;
        $this->statusPresentationRepo = $statusPresentationRepo;                    
        $this->loginService = $loginService;
   
        $this->fileLogger = $fileLogger;        
    } 
    
 
    
      
    #[\Override]   
    public function validateUser (ServerRequestInterface $request): void {                   
             /** @var SecurityInterface $security */
        $security = $this->statusSecurityRepo->get();  //
        if (isset($security)) {
            $loginAgregate = $security->getLoginAggregate();                 
            $validatedUserName = $loginAgregate?->getLoginName();
            
            if (isset($validatedUserName))  {   
                //$validatedUserName="Xxx";
                try {
                    //validovat                    
                    $this->validateUserByAuthServer($request, $validatedUserName);
                    $this->loginService->setAddUserNameToEventsLogin($validatedUserName);
//                    $this->fileLogger?->notice("* " . $validatedUserName . " je validní uživatel( je v single_login)," .
//                                       ' pokud nebyl v tabulce events.login. -> tak tam byl přidán.' ); 
                    //není-li ve statusu security (prvni request, jeste nezvalidovano), poznamenat do statusu security 
                    if (!$security->isUserNameVerifiedWithinSession($validatedUserName)) {
                        $security->addUserNameVerifiedWithinSession($validatedUserName);
                    }                    
                }
                catch (ValidatingException $e) {
                    $security->removeContext();   
                    $this->loginService->setDeleteUserNameFromEventsLogin($validatedUserName);
//                    $this->fileLogger?->notice("* " . $validatedUserName . " není validní uživatel (není v single_login)." .
//                                                       " - a byl 'vymazán' z tabulky events.login (byl-li tam)" );                         
                    $this->fileLogger?->error($e->getMessage());  
                }  
                catch (ConnectionException $e) {
                    $security->removeContext();   
                    $this->fileLogger?->error($e->getMessage());  
                }
            }
            else{   //neni  $validatedUserName                  
                $this->fileLogger?->notice("Nevalidováno - Nikdo není přihlašený." );                                
            }   
        }                
    }
                          
    
  
    /**
     * 
     * @param ServerRequestInterface $request
     * @param SecurityInterface $security
     * @param string $validatedUserName
     * @return void
     */
    private function validateUserByAuthServer( ServerRequestInterface $request, string $validatedUserName ):void {                               

        $scheme = $request->getUri()->getScheme();
        $host = $request->getUri()->getHost();
        $ruri = $this->getUriInfo($request)->getRestUri();
        $rap =$this->getUriInfo($request)->getRootAbsolutePath();
        $sp = $this->getUriInfo($request)->getSubdomainPath();        
        $url = "$scheme://$host$sp"."auth/v1/validuser";
        // options pro stream_context_create() vždy definuj s položkou http
        // url adresu pro file_get_contents(url, ..) definuj: https://....
        // use key 'http' even if you send the request to https://...
        $json = json_encode([$validatedUserName]);
        $options = [
            'http' => [
                'header' => "Content-type: application/json",
                //'header' => "Cookie: XDEBUG_SESSION=netbeans-xdebug\r\n",
                'method' => 'POST' ,
                'content' => $json,
            ],
        ];        
        //--------------      
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);   //posle  na url, vysledek z auth ...content pak priradi do result      
        //--------------        
        //** zkouska    $result=false;
        if ($result!==false) {
            $resultData = json_decode($result, true);            
            if ($resultData ['validFromAuth'] == 'invalidUser')  {
                throw new ValidatingException("validateUserByAuthServer: Uživatel není validní.");              
            }        
        } else {                                                      
            throw new ConnectionException("validateUserByAuthServer: Spojeni se nezdařilo. Nelze validovat(ověřit) uživatele.");            
        }                               
    }
    
            
    
    
     /**
     * Pomocná metoda - získá base path z objektu UriInfo, který byl vložen do requestu jako atribut s jménem AppFactory::URI_INFO_ATTRIBUTE_NAME v AppFactory.
     *
     * @return UriInfoInterface
     */
     private function getUriInfo(ServerRequestInterface $request): UriInfoInterface {
        $uriInfo = $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME);
        if (! $uriInfo instanceof UriInfoInterface) {
            throw new LogicException("Atribut requestu ".AppFactory::URI_INFO_ATTRIBUTE_NAME." neobsahuje objekt typu ".UriInfoInterface::class.".");
        }
        return $uriInfo;
    }
    
     
}
