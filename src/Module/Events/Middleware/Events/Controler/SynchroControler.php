<?php
namespace Events\Middleware\Events\Controler;

use Site\ConfigurationCache;

use FrontControler\FrontControlerAbstract;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Status\Model\Entity\SecurityInterface;

use Events\Model\Repository\LoginRepoInterface;
use Events\Model\Entity\LoginInterface;
use Events\Model\Entity\Login;

use Status\Model\Enum\FlashSeverityEnum;

use Events\Service\ValidateServiceInterface;
use Events\Service\ValidatingService;
use Events\Service\LoginService;
use Events\Service\LoginServiceInterface;


use Psr\Http\Message\ServerRequestInterface;

use Psr\Http\Message\UploadedFileInterface;
use Pes\Http\Helper\RequestStatus;
use Pes\Http\Request\RequestParams;

use Pes\Http\Factory\ResponseFactory;
use Pes\Http\Response;



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

    
    
    
    
    
    
    /**/  
    /**
     * volani routy "events/v1/synchro" , button Synchro  
     * doptava se na "auth/v1/synchro" , "maze"  a pridava loginName do db Events, tabulka login
     * 
     * @param ServerRequestInterface $request
     * @return type
     */
    public function synchro  (ServerRequestInterface $request){         
       //  $controlledItems - obsah zavisle db (Events), a ten se upravuje podle referencmi db (single_login), v Auth\...\SynchroControler.php
       //  viz routa "auth/v1/synchro"        
        
             /** @var SecurityInterface $security */
        $security = $this->statusSecurityRepo->get();  //
        if (isset($security)) {
                //$loginAgregate = $security->getLoginAggregate();            





            $logins = $this->loginRepo->findAll();   // pole entit     
            $controlledItems=[];
                  /** @var LoginInterface $login */
            foreach ($logins as $login) {
                if ($login->getDeletedDueToAuth()=='0') {
                    $controlledItems[$login->getLoginName()] = $login->getLoginName();
                }
            }        
            //--------------
            $scheme = $request->getUri()->getScheme();
            $host = $request->getUri()->getHost();
            $ruri = $this->getUriInfo($request)->getRestUri();
            $rap =$this->getUriInfo($request)->getRootAbsolutePath();
            $sp = $this->getUriInfo($request)->getSubdomainPath();        
            $url = "$scheme://$host$sp"."auth/v1/synchro";
            // options pro stream_context_create() vždy definuj s položkou http
            // url adresu pro file_get_contents(url, ..) definuj: https://....
            // use key 'http' even if you send the request to https://...
            $json = json_encode($controlledItems);
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
                    /** @var ValidateServiceInterface  $serviceValidate */
                $serviceValidate = $this->container->get(ValidatingService::class);
                    /** @var LoginServiceInterface  $serviceLogin */
                $serviceLogin = $this->container->get(LoginService::class);

                //if ( $resultData['remItems'] ) {   
                //if (!empty($resultData['remItems'])) {
                if ($resultData ['remItems']?? [])      {               
                    foreach ($resultData['remItems'] as  $loginName) {                                
                        //tady nemazat, ale zapsat priznak deleted_due_to_auth, a prejmenovat,t.j. pridat retezec datacasu  date("Ymd_His")                          
                          $serviceLogin->setDeleteUserNameFromEventsLogin($loginName);
                    }                                                            
                }    
                $this->loginRepo->flush();

                //if ( $resultData['addItems'] ) {        
                //if (!empty($resultData['addItems'])) {
                if ($resultData ['addItems']?? [])      {         
                    foreach ($resultData['addItems'] as $loginName) {                                  
                        $serviceLogin->setAddUserNameToEventsLogin($loginName);
                    }
                }                         
            } else {
                $this->addFlashMessage("Spojeni se nezdařilo. Nelze synchronizovat uživatele.", FlashSeverityEnum::ERROR);
            } 
        
        }
      
        return $this->redirectSeeLastGet($request); // 303 See Other
    }      
 
    
    
    
    //-----------------------------------------------------------------------------------------------------------------

    /**  z buttonu */
    /**
     * volano z routy "events/v1/validateuser" , button ValidateUser 
     * zjisti validUser\ invalidUser\ noUser
     * 
     * @param ServerRequestInterface $request
     * @return type
     */
    public function validateUser (ServerRequestInterface $request){              
        $val = $this->ValidUser($request);        
        switch ($val) {
            case 'validUser':
               $this->addFlashMessage("Přihlašený je validní uživatel v single_login. - Events\SynchroControler",  FlashSeverityEnum::SUCCESS);
                break;
            case 'invalidUser':
                $this->addFlashMessage("Přihlašený není validní uživatel v single_login. - Events\SynchroControler",  FlashSeverityEnum::ERROR);
                break;
            case 'noUser':
                $this->addFlashMessage("Nikdo není přihlášen. - Events\SynchroControler",  FlashSeverityEnum::ERROR );               
                break;
        }       
        return $this->redirectSeeLastGet($request); // 303 See Other
    }     
    
    
    
    
    
    /** */  
    /**
     * Doptava se na  route auth/v1/validuser na loginName  přihlášeného uživatele
     * 
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function validUser (ServerRequestInterface $request): string{                       
        /** @var SecurityInterface $statusPresentation */
        $statusPresentation = $this->statusSecurityRepo->get();
        $loginAgregate = $statusPresentation->getLoginAggregate();
        
        if (isset($loginAgregate))  {
            $validatedUserName = $loginAgregate->getLoginName();

            //--------------
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
                $this->addFlashMessage("Spojeni se nezdařilo. Nelze validovat(ověřit) uživatele.", FlashSeverityEnum::ERROR);
            }

            $res = $resultData ['validFromAuth'];   //'validUser' | 'invalidUser'
        }
        else  {
            $res = 'noUser';
        }
        
        return $res;
        
    }      
          
    
    
    
}

