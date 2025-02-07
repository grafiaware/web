<?php
namespace Events\Middleware\Events\Controler;

use Site\ConfigurationCache;

use FrontControler\FrontControlerAbstract;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

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
  //  private $visitorProfileRepo;
  //  private $visitorJobRequestRepo;
        

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo
//            DocumentRepo $documentRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);        
//        $this->documentRepo = $documentRepo;
    }

    
    
    public function synchro (ServerRequestInterface $request){
        $scheme = $request->getUri()->getScheme();
        $host = $request->getUri()->getHost();

        $ruri = $this->getUriInfo($request)->getRestUri();
        $rap =$this->getUriInfo($request)->getRootAbsolutePath();
        $sp = $this->getUriInfo($request)->getSubdomainPath();
        
        $url = "$scheme://$host$sp"."auth/v1/synchro";
        //$data = ['companyName' => $companyName, 'loginName' => $loginName ];

        // options pro stream_context_create() vždy definuj s položkou http
        // url adresu pro file_get_contents(url, ..) definuj: https://....
        // use key 'http' even if you send the request to https://...
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded",
                'method' => 'POST' //,
                //'content' => http_build_query($data),
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === false) {
           $this->addFlashMessage("Spojeni synchro se nezdařilo.", FlashSeverityEnum::ERROR);
        }
        else {
            $this->addFlashMessage("Přišlo $result", FlashSeverityEnum::SUCCESS);            
        }
        
        
        if ($result === false) {
            $this->addFlashMessage("Mail o dokončení registrace se nepodařilo odeslat.", FlashSeverityEnum::WARNING);
        } else {
            return true ;     
        }
        
        return $this->redirectSeeLastGet($request); // 303 See Other

    }
    
    
          
}

