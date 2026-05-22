<?php
namespace Events\Service;

use Events\Service\ValidateServiceInterface;

use Pes\Application\UriInfoInterface;
use Psr\Http\Message\ServerRequestInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Pes\Application\AppFactory;
use LogicException;


/**
 * Description of Validate
 *
 * @author vlse2610
 */
class ValidateService implements ValidateServiceInterface {              
    private $statusSecurityRepo;
    private $statusFlashRepo;
    private $statusPresentationRepo;
     
     
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo            
             ) {        
            
        $this->statusSecurityRepo = $statusSecurityRepo;
        $this->statusFlashRepo = $statusFlashRepo;
        $this->statusPresentationRepo = $statusPresentationRepo;          
    } 
    
 
    
      
    #[\Override]
    public function validUser (ServerRequestInterface $request): string {  
                
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
