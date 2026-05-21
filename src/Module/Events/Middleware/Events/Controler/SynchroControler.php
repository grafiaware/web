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

    
    
     public function synchro (ServerRequestInterface $request){              
        $val = $this->ValidUser($request);

        
        switch ($val) {
            case 'validUser':
               $this->addFlashMessage("Přihlašený je validní uživatel v single_login.",  FlashSeverityEnum::SUCCESS);
                break;
            case 'invalidUser':
                $this->addFlashMessage("Přihlašený není validní uživatel v single_login.",  FlashSeverityEnum::ERROR);
                break;
            case 'noUser':
                $this->addFlashMessage("Nikdo není přihlášen.",  FlashSeverityEnum::ERROR );               
                break;
        }
        
        return $this->redirectSeeLastGet($request); // 303 See Other
    }     
    
    
    
    
    
    
    
    public function synchro_spravne (ServerRequestInterface $request){         
       //  $controlledItems - obsah zavisle db (Events), a ten se upravuje podle referencmi db (single_login), v Auth\...\SynchroControler.php
       //  viz routa "auth/v1/synchro"
        
        
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
            
            //if ( $resultData['remItems'] ) {   
            //if (!empty($resultData['remItems'])) {
            if ($resultData ['remItems']?? [])      {               
                foreach ($resultData['remItems'] as  $loginName) {                                
//                    $loginA =  new Login(); $loginA->setLoginName($loginItem); $this->loginRepo->remove($loginA);                    
                    //tady nemazat, ale zapsat priznak deleted_due_to_auth, a prejmenovat,t.j. pridat retezec datacasu  date("Ymd_His")
                    $loginA = $this->loginRepo->get($loginName);                    
                    $loginA->setDeletedDueToAuth('1');
                    $loginA->setLoginName($loginName . '_deleted_' . date("Ymd_His") );                    
                }                                                            
            }    
            $this->loginRepo->flush();
            
            //if ( $resultData['addItems'] ) {        
            //if (!empty($resultData['addItems'])) {
            if ($resultData ['addItems']?? [])      {         
                foreach ($resultData['addItems'] as $loginItem) {                                
                    $loginA =  new Login();
                    $loginA->setDeletedDueToAuth(0);
                    $loginA->setLoginName($loginItem); 
                    
                    $this->loginRepo->add($loginA);
                }
            }                         
        } else {
            $this->addFlashMessage("Spojeni se nezdařilo. Nelze synchronizovat uživatele.", FlashSeverityEnum::ERROR);
        }                
        return $this->redirectSeeLastGet($request); // 303 See Other
    }      
 
    
    
    
      
    protected function ValidUser (ServerRequestInterface $request){                       
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

            $res = $resultData ['valid'];
        }
        else  {
            $res = 'noUser';
        }
        
        return $res;
        
    }      
          
    
    
    
}

