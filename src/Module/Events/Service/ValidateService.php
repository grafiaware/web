<?php
namespace Events\Service;

use Events\Service\ValidateServiceInterface;

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
class ValidateService implements ValidateServiceInterface {              
    private $statusSecurityRepo;
    private $statusFlashRepo;
    private $statusPresentationRepo;
    private $fileLogger;
    
    private $loginRepo;
     
     
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,               
            LoginRepoInterface $loginRepo,    
            
            ?FileLogger $fileLogger = null
            ) {        
            
        $this->statusSecurityRepo = $statusSecurityRepo;
        $this->statusFlashRepo = $statusFlashRepo;
        $this->statusPresentationRepo = $statusPresentationRepo;                    
        $this->loginRepo = $loginRepo;
   
        $this->fileLogger = $fileLogger;        
    } 
    
 
    
      
    #[\Override]
    /**
     * Validuje uživatele.
    * Neni-li ve statusu security (tj. ještě nezvalidováno, jedná se o první request ), poznamená do statusu security.   

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
                    $userStatus = $this->isUserValid($request, $security, $validatedUserName);      /*  **zkouska* */  // $userStatus = 'invalidUser';

                    if ($this->isUserValid($request, $security, $validatedUserName)) {
                    
                        $this->addTologin_userNameIsValid($validatedUserName);                          
                        //není-li ve statusu security (prvni request, jeste nezvalidovano), poznamenat do statusu security 
                        if (!$security->isUserNameVerifiedWithinSession($validatedUserName)) {
                            $security->addUserNameVerifiedWithinSession($validatedUserName);
                        }                    
                    }
                    else{
                        $this->deleteFromLogin_userNameIsNotValid($validatedUserName);                                     
                    }
                }
                catch (ValidatingException $e)
                {
                    $this->fileLogger?->error("isUserValid: Spojeni se nezdařilo. Nelze validovat(ověřit) uživatele.");  
                }
   
            }
           
        }
        else{   //neni  $validatedUserName                  
            $this->fileLogger?->notice("Nevalidováno - Nikdo není přihlašený. "  );                                
        }           
      
    }
        
      
    
    /**
     * Název prihlašeného $validatedUserName je validni (je v single-login.login tabulce).
     * Není-li v events.login tabulce, zapiše do events.login tabulky.
     *  
     * @param type $validatedUserName
     * @return void
     */
    protected function addTologin_userNameIsValid($validatedUserName): void {
         //kdyz prihlaseny neni v events.login tabulce a je validni, tak zapsat do events login tabulky                     
        $login = $this->loginRepo->get($validatedUserName);
        if ($login) {                            
            $this->fileLogger?->notice("Přihlašený " .$validatedUserName . " je validní uživatel(v single_login)." .
                                      " a je v tabulce events.login." );                                                                 
        }
        else {
            $this->addUserNameToEvents($validatedUserName);
            $this->fileLogger?->notice("Přihlašený " .$validatedUserName . " je validní uživatel(v single_login)," .
                                       ' nebyl v tabulce events.login. -> a byl tam přidán.' );                             
        }                       
    }
    
    
    
    /**
     * Název prihlašeného $validatedUserName není validni (není v single-login.login tabulce).
     * Smaže  $security. "Vymaže" název z tabulky events.login (tím, že změní název přidáním 'deleted'), byl-li tam.
     * 
     * @param type $validatedUserName
     */
    protected function deleteFromLogin_userNameIsNotValid($validatedUserName) : void {
        // smazat v statusu security
        $security->removeContext();                                                
        // "vymazat" z tabulky events.login ,tj. pridat "deleted" --                     
        $login = $this->loginRepo->get($validatedUserName);
        if ($login) {
                $this->deleteUserNameFromEvents($login);           
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
    
    
    #[\Override]
    public function deleteUserNameFromEvents(LoginInterface $login): void {        
        //$loginA = $this->loginRepo->get($loginName);                    
        $login->setDeletedDueToAuth('1');
        $login->setLoginName($login->getLoginName() . '_deleted_' . date("Ymd_His") );                 
    }
        
    #[\Override]
    public function addUserNameToEvents(string $loginName): void {        
        /**  @var LoginInterface $loginA */
        $loginA =  new Login();
        $loginA->setDeletedDueToAuth(0);
        $loginA->setLoginName($loginName); 

        $this->loginRepo->add($loginA);        
    }
        
    
    
}
