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
use Status\Model\Enum\FlashSeverityEnum;



use Auth\Model\Repository\LoginAggregateRegistrationRepo;
use Auth\Model\Repository\LoginAggregateCredentialsRepo;

use Auth\Model\Entity\Credentials;
use Auth\Model\Entity\LoginAggregateCredentials;
use Auth\Model\Entity\Registration;
use Auth\Model\Entity\LoginAggregateRegistration;




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
        
        
//        $requestParams = new RequestParams();
//        $companyName = $requestParams->getParsedBodyParam($request, 'companyName');
//        $loginName = $requestParams->getParsedBodyParam($request, 'loginName');   
//               
//        $loginAggregateRegistration = $this->loginAggregateRegistrationRepo->get($loginName);
//        $registerEmail = $loginAggregateRegistration->getRegistration()->getEmail();

       
//            try {
//                    $ret = $mail->mail($params); // posle mail
//                } catch (MailExceptionInterface $exc) {
//                    echo $exc->getTraceAsString();
//                }  

        
    
        return $this->createStringOKResponse("Byl jsem v AUTH Synchro", 200); // 303 See Other
         
        
        
        
        
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
//
//        $context = stream_context_create($options);
//        $result = file_get_contents($url, false, $context);
//        if ($result === false) {
//            $this->addFlashMessage("Mail o dokončení registrace se nepodařilo odeslat.", FlashSeverityEnum::WARNING);
//        } else {
//            return true ;     
//        }
        
    
    }
    
    
          
}

