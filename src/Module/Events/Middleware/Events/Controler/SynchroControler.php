<?php
namespace Events\Middleware\Events\Controler;

use Site\ConfigurationCache;

use FrontControler\FrontControlerAbstract;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
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
       //  $controlledItems - obsah zavisle db, a ten se upravuje podle referencmi db, 
       //  viz routa "auth/v1/synchro"
        
        
        $logins = $this->loginRepo->findAll();        
        $controlledItems=[];
              /** @var LoginInterface $login */
        foreach ($logins as $login) {
            $controlledItems[] = $login->getLoginName();
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
                'method' => 'POST' ,
                'content' => $json,
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);        
        //--------------
        if ($result!==false) {
            $resultData = json_decode($result, true);            
            
            if ( $resultData['addItems'] ) {        
                              /** @var LoginInterface $loginItem */
                foreach ($resultData['addItems'] as $key => $loginItem) {                                
                    $loginA =  new Login();
                    $loginA->setLoginName($key);
                    $loginA->setRole($loginItem['role']);
                    $loginA->setEmail($loginItem['email']);
                    $loginA->setInfo($loginItem['info']);
                    $loginA->setModul($loginItem['modul']);
                    
                    $this->loginRepo->add($loginA);
                }
            }
            if ( $resultData['remItems'] ) {                                           
                foreach ($resultData['remItems'] as  $loginItem) {                                
                    $loginA =  new Login();
                    $loginA->setLoginName($loginItem);
                    
                    $this->loginRepo->remove($loginA);
                }
            }    
        } else {
            $this->addFlashMessage("Spojeni synchro se nezdařilo.", FlashSeverityEnum::ERROR);
        }
                
        return $this->redirectSeeLastGet($request); // 303 See Other
    }      
          
}

        
//        $scheme = $request->getUri()->getScheme();
//        $host = $request->getUri()->getHost();
//
//        $ruri = $this->getUriInfo($request)->getRestUri();
//        $rap =$this->getUriInfo($request)->getRootAbsolutePath();
//        $sp = $this->getUriInfo($request)->getSubdomainPath();        
//        $url = "$scheme://$host$sp"."auth/v1/synchro";
//        //$data = ['companyName' => $companyName, 'loginName' => $loginName ];
//
//        // options pro stream_context_create() vždy definuj s položkou http
//        // url adresu pro file_get_contents(url, ..) definuj: https://....
//        // use key 'http' even if you send the request to https://...
//        $options = [
//            'http' => [
//                'header' => "Content-type: application/x-www-form-urlencoded",
//                'method' => 'POST' //,
//                //'content' => http_build_query($data),
//            ],
//        ];
//        $context = stream_context_create($options);
//        $result = file_get_contents($url, false, $context);
//        if ($result === false) {
//           $this->addFlashMessage("Spojeni synchro se nezdařilo.", FlashSeverityEnum::ERROR);
//        }
//        else {
//            $this->addFlashMessage("Přišlo ** $result **", FlashSeverityEnum::SUCCESS);            
//        }
                        