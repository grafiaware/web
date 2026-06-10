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
    private $loginRepo;
    private $loginService;
    
    private $fileLogger;
    
    
     
     
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,               
            LoginRepoInterface $loginRepo,    
            LoginServiceInterface $loginService,
            
            ?FileLogger $fileLogger = null
            ) {        
            
        $this->statusSecurityRepo = $statusSecurityRepo;
        $this->statusFlashRepo = $statusFlashRepo;
        $this->statusPresentationRepo = $statusPresentationRepo;                    
        $this->loginRepo = $loginRepo;
        $this->loginService = $loginService;
   
        $this->fileLogger = $fileLogger;        
    } 
    
 
    
      
    #[\Override]
    /**
     * Volá se  z middleware ValidateUser.
     * Validuje uživatele.
     * Neni-li uživatel ve statusu security, tj. ještě nezvalidováno, jedná se o první request.
     * Že zvalidováno, poznamená do statusu security.   
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
                   // $userStatus = $this->isUserValid($request, $security, $validatedUserName);      /*  **zkouska* */  // $userStatus = 'invalidUser';

                    if ($this->isUserValid($request, $security, $validatedUserName)) {
                    
                        $this->addToLogin($validatedUserName);                          
                        //není-li ve statusu security (prvni request, jeste nezvalidovano), poznamenat do statusu security 
                        if (!$security->isUserNameVerifiedWithinSession($validatedUserName)) {
                            $security->addUserNameVerifiedWithinSession($validatedUserName);
                        }                    
                    }
                    else { 
                        $this->deleteFromLogin($validatedUserName, $security );                                     
                    }
                }
                catch (ValidatingException $e) {
                    $this->fileLogger?->error("isUserValid: Spojeni se nezdařilo. Nelze validovat(ověřit) uživatele.");  
                }   
            }
            else{   //neni  $validatedUserName                  
                $this->fileLogger?->notice("Nevalidováno - Nikdo není přihlašený. "  );                                
            }   
           
        }                
      
    }
        
      
    
    /**
     * Zavoláno v případě, že název prihlašeného $validatedUserName JE validni (je v single-login.login tabulce).
     * Není-li v events.login tabulce, zapiše do events.login tabulky.
     *       
     * @param string $validatedUserName
     * @return void
     */
    protected function addToLogin(string $validatedUserName): void {
         //kdyz prihlaseny je validni a neni v events.login tabulce, tak zapsat do events login tabulky                     
        $login = $this->loginRepo->get($validatedUserName);
        if ($login) {                            
            $this->fileLogger?->notice("Přihlašený " .$validatedUserName . " je validní uživatel(v single_login)." .
                                      " a je v tabulce events.login." );                                                                 
        }
        else {  
            
            $this->loginService->setAddUserNameToEventsLogin($login /*$validatedUserName*/);                        
            $this->fileLogger?->notice("Přihlašený " .$validatedUserName . " je validní uživatel(v single_login)," .
                                       ' nebyl v tabulce events.login. -> a byl tam přidán.' );                             
        }                       
    }
    
    
    
    /**
     * Zavoláno v případě, že název prihlašeného $validatedUserName NENÍ validni (není v single-login.login tabulce).
     * Odstraní ze $security. "Vymaže" název z tabulky events.login, byl-li tam.
     *    
     * @param string $validatedUserName
     * @param SecurityInterface $security
     * @return void
     */
    protected function deleteFromLogin(string $validatedUserName, SecurityInterface $security) : void {         
        $security->removeContext();                                                
        $login = $this->loginRepo->get($validatedUserName);
        if ($login) {                                                
                 $this->loginService->setDeleteUserNameFromEventsLogin($login);           
        }                       
        $this->fileLogger?->notice("Přihlašený " .$validatedUserName . " není validní uživatel (v single_login)." .
                                   " - a byl vymazán z tabulky events.login (byl-li tam)" );                                       
    }
    
    
    
    
  
    /**
     * 
     * @param ServerRequestInterface $request
     * @param SecurityInterface $security
     * @param string $validatedUserName
     * @return void
     */
    protected function isUserValid( ServerRequestInterface $request, SecurityInterface $security, string $validatedUserName ):bool {                               

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
        $res = '';
        if ($result!==false) {
            $resultData = json_decode($result, true);       
            if ($resultData ['validFromAuth'] == 'validUser')    {$res=true;}
            if ($resultData ['validFromAuth'] == 'invalidUser')  {$res=false;}        
        } else {                                                      
            throw new ValidatingException("isUserValid: Spojeni se nezdařilo. Nelze validovat(ověřit) uživatele.");            
        }
                       
        return $res; 
    }
    
            
    
    
     /**
     * Pomocná metoda - získá base path z objektu UriInfo, který byl vložen do requestu jako atribut s jménem AppFactory::URI_INFO_ATTRIBUTE_NAME v AppFactory.
     *
     * @return UriInfoInterface
     */
    protected function getUriInfo(ServerRequestInterface $request): UriInfoInterface {
        $uriInfo = $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME);
        if (! $uriInfo instanceof UriInfoInterface) {
            throw new LogicException("Atribut requestu ".AppFactory::URI_INFO_ATTRIBUTE_NAME." neobsahuje objekt typu ".UriInfoInterface::class.".");
        }
        return $uriInfo;
    }
    
     
}
