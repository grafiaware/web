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
         //$rH = $request->getHeaders();
        
        /** @var SecurityInterface $statusSecurity */
        $statusSecurity = $this->statusSecurityRepo->get();
        $loginAgregate = $statusSecurity->getLoginAggregate();
        
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
                $this->statusFlashRepo->get()->setMessage("Spojeni se nezdařilo. Nelze validovat(ověřit) uživatele.", FlashSeverityEnum::ERROR);
                //zapsat do logu
                // co s requestem ?
            }

            $res = $resultData ['validFromAuth'];   //'validUser' | 'invalidUser'
      
        }
        else  {
            $res = 'noUser';
        }
                
       //*************************
        $res='invalidUser';
        $validatedUserName = 'XxX';       
       //************************* 
        
        switch ($res) {
            case 'noUser':
                $this->statusFlashRepo->get()->setMessage("Nikdo není přihlášen. - service",  FlashSeverityEnum::ERROR );               
                break;
            
            case 'validUser':
                $this->statusFlashRepo->get()->setMessage("Přihlašený je validní uživatel v single_login. - service",  FlashSeverityEnum::SUCCESS);
                
                //kdyz neni prihlaseny neni v events.login tabulce a ja ok, tak zapsat do events login tabulky                    
                break;
           
            case 'invalidUser':                                
                // smazat v statusuSecurity
                $statusSecurity->removeContext();
            
                // LOGOVAT
                if ( $this->fileLogger ) {
                    $this->fileLogger->error("*Přihlašený " .$validatedUserName . " není validní uživatel (v single_login)." . "- result z auth byl - " . $res );
                }

                // "vymazat" z tabulky login events,tj. pridat "delete" --   
                if ($validatedUserName) {
                    $login = $this->loginRepo->get($validatedUserName);
                    if ($login) {
                        $this->deleteUserNameFromEvents($validatedUserName);           
                       
                        $this->loginRepo->flush(); //??????
                    }
                    
                }

                                
       /**/     $this->statusFlashRepo->get()->setMessage("Přihlašený není validní uživatel v single_login. - service",  FlashSeverityEnum::ERROR);       
                break;            
                 
        
        
            
            
        }
        
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
    
    
    public function deleteUserNameFromEvents($loginName): void {
        
        $loginA = $this->loginRepo->get($loginName);                    
        $loginA->setDeletedDueToAuth('1');
        $loginA->setLoginName($loginName . '_deleted_' . date("Ymd_His") ); 
        
        
    }
    
    
    public function addUserNameToEvents($loginName): void {
        
        $loginA =  new Login();
        $loginA->setDeletedDueToAuth(0);
        $loginA->setLoginName($loginName); 

        $this->loginRepo->add($loginA);
        
    }
    
    
    
}
