<?php
namespace Events\Service;

use Events\Service\ValidateServiceInterface;

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
    public function validateUser (ServerRequestInterface $request): void {   
             /** @var SecurityInterface $security */
        $security = $this->statusSecurityRepo->get();  //
        $loginAgregate = $security->getLoginAggregate();        
        $validatedUserName = $loginAgregate?->getLoginName();
        $userStatus = $this->userStatus($request, $security, $validatedUserName); 
        
        //*************************               
//                    $userStatus='invalidUser';
//                    $validatedUserName = 'XxX';       
        //*************************  

        switch ($userStatus) {
                    case 'noUser':
                        // logovat
                        if ($this->fileLogger ) {
                            $this->fileLogger->error("Nikdo není přihlašený "  ." - result z auth byl: " . $userStatus  );
                        }                        
                    break;

                    case 'validUser':
                        //kdyz  prihlaseny neni v events.login tabulce a je ok, tak zapsat do events login tabulky, 
                        if ($validatedUserName) {
                            $login = $this->loginRepo->get($validatedUserName);
                            if ($login) {
                                if ($this->fileLogger ) {
                                    $this->fileLogger->error("Přihlašený " .$validatedUserName . " je validní uživatel(v single_login)." .
                                                     " - result z auth byl: " . $userStatus . ' a je v tabulce events.login.' );
                                }                                     
                            }
                            else {
                                $this->addUserNameToEvents($validatedUserName);
                                if ($this->fileLogger ) {
                                    $this->fileLogger->error("Přihlašený " .$validatedUserName . " je validní uživatel(v single_login)." .
                                                     " - result z auth byl: " . $userStatus . ' a nebyl v tabulce events.login. -> a byl tam přidán.' );
                                } 
                            }                            
                        }                          

                        
                        //neni-li ve statusu, poznamenat do statusu
                        if (!$security->isUserNameVerifiedWithinSession($validatedUserName)) {
                            $security->addUserNameVerifiedWithinSession($validatedUserName);
                        }

                    break;
                    

                    case 'invalidUser':                              
                        if ($security)  {
                            // smazat v statusuSecurity
                            $security->removeContext();
                        }                        

                        // "vymazat" z tabulky login events,tj. pridat "delete" --   
                        if ($validatedUserName) {
                            $login = $this->loginRepo->get($validatedUserName);
                            if ($login) {
                                $this->deleteUserNameFromEvents($login);           

                                //$this->loginRepo->flush(); //??????
                            }
                        }
                        // logovat
                        if (isset($this->fileLogger )) {
                            $this->fileLogger->error("Přihlašený " .$validatedUserName . " není validní uživatel (v single_login)." .
                                                     " - result z auth byl: " . $userStatus . ' a byl vymazan z tabulky events.login (byl-li tam)' );
                        }                        
                    break;            
                }  
                
    }      
        
    
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @param SecurityInterface $security
     * @param string|null $validatedUserName
     * @return string
     */
    protected function userStatus( ServerRequestInterface $request, SecurityInterface $security, string|null $validatedUserName ) : string {               
            
        if (isset($validatedUserName))  {    
            //---------------------------------------------
            if (!$security->isUserNameVerifiedWithinSession )  {  // prvni request, jeste nezvalidovano    
                
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
                    if ($result!==false) {
                        $resultData = json_decode($result, true);                                                
                    } else {
                        $this->statusFlashRepo->get()->setMessage("Spojeni se nezdařilo. Nelze validovat(ověřit) uživatele.", FlashSeverityEnum::ERROR);
                        //zapsat do logu
                        // neco s requestem ???????   ???
                    }
                    $res = $resultData ['validFromAuth'];   //'validUser' | 'invalidUser'           
            } //-------------------------------
                        
        }
        else  {
            $res = 'noUser';
        }
                         
        
        //***                
        return $res; 
        //***
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
    
    
    
//    
//    protected function isFirstRequest(SecurityInterface $statusSecurity){
//                    
//            /** @var Security $statusSecurity */
//    return $statusSecurity->getInfo($name);
//               
//    }
    
    
    
}
