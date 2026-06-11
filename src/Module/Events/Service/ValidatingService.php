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

use Events\Model\Repository\LoginRepoInterface;
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
           // LoginRepoInterface $loginRepo,    
            LoginServiceInterface $loginService,
            
            ?FileLogger $fileLogger = null
            ) {        
            
        $this->statusSecurityRepo = $statusSecurityRepo;
        $this->statusFlashRepo = $statusFlashRepo;
        $this->statusPresentationRepo = $statusPresentationRepo;                    
        //$this->loginRepo = $loginRepo;
        $this->loginService = $loginService;
   
        $this->fileLogger = $fileLogger;        
    } 
    
 
    
      
    #[\Override]
    /**
     * Volá se  z middleware ValidateUser.
     * Validuje uživatele.
     * Neni-li uživatel ve statusu security, tj. ještě nezvalidováno, jedná se o první request.
     * Že je zvalidováno, poznamená do statusu security.   
     *     
     * @param ServerRequestInterface $request
     * @return void
     */    
    public function validateUser (ServerRequestInterface $request): void {           
        
             /** @var SecurityInterface $security */
        $security = $this->statusSecurityRepo->get();  //
        if (isset($security)) {
            $loginAgregate = $security->getLoginAggregate();                 
            $validatedUserName = $loginAgregate?->getLoginName();
            
            if (isset($validatedUserName))  {         
                try {
                    //validovat                    
                    $this->validateUserByAuthServer($request, $validatedUserName);
                    $this->loginService->setAddUserNameToEventsLogin($validatedUserName);
                    //není-li ve statusu security (prvni request, jeste nezvalidovano), poznamenat do statusu security 
                    if (!$security->isUserNameVerifiedWithinSession($validatedUserName)) {
                        $security->addUserNameVerifiedWithinSession($validatedUserName);
                    }                    
                }
                catch (ValidatingException $e) {
                    $security->removeContext();   
                    $this->loginService->setDeleteUserNameFromEventsLogin($validatedUserName);
                    $this->fileLogger?->error($e->getMessage());  
                }  
                catch (ConnectionException $e) {
                    $security->removeContext();   
                    $this->fileLogger?->error($e->getMessage());  
                }
            }
            else{   //neni  $validatedUserName                  
                $this->fileLogger?->notice("Nevalidováno - Nikdo není přihlašený. "  );                                
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
        
 //  /*  ** zkouska */     $result=false;
        //$res = '';
        if ($result!==false) {
            $resultData = json_decode($result, true);       
          //  if ($resultData ['validFromAuth'] == 'validUser')    {$res=true;}
            if ($resultData ['validFromAuth'] == 'invalidUser')  {
                //$res=false;
                throw new ValidatingException("isUserValid: Uživatel není validní.");              
            }        
        } else {                                                      
            throw new ConnectionException("isUserValid: Spojeni se nezdařilo. Nelze validovat(ověřit) uživatele.");            
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
