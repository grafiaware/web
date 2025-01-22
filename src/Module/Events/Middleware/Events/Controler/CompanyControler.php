<?php

namespace Events\Middleware\Events\Controler;

use Site\ConfigurationCache;
use FrontControler\FrontControlerAbstract;
use Auth\Model\Entity\LoginAggregateFullInterface;

use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Events\Model\Repository\DocumentRepo;
use Events\Model\Repository\DocumentRepoInterface;
use Events\Model\Repository\RepresentativeRepoInterface;
use Events\Model\Repository\RepresentativeRepo;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyContactRepoInterface;
use Events\Model\Repository\CompanyAddressRepoInterface;
use Events\Model\Repository\CompanyInfoRepoInterface;
use Events\Model\Repository\CompanytoNetworkRepoInterface;
use Events\Model\Repository\NetworkRepoInterface;

use Events\Model\Entity\CompanyInterface;
use Events\Model\Entity\Company;
use Events\Model\Entity\CompanyContactInterface;
use Events\Model\Entity\CompanyContact;
use Events\Model\Entity\CompanyAddressInterface;
use Events\Model\Entity\CompanyAddress;
use Events\Model\Entity\CompanyInfoInterface;
use Events\Model\Entity\CompanyInfo;
use Events\Model\Entity\CompanytoNetworkInterface;
use Events\Model\Entity\CompanytoNetwork;
use Events\Model\Entity\NetworkInterface;
use Events\Model\Entity\Network;
use Events\Model\Entity\RepresentativeInterface;
use Events\Model\Entity\Representative;

use Status\Model\Enum\FlashSeverityEnum;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Pes\Http\Helper\RequestStatus;
use Pes\Http\Request\RequestParams;
use Pes\Http\Factory\ResponseFactory;
use Pes\Http\Response;

use UnexpectedValueException;


/**
 * Description of NestedFilesUpload
 *
 * @author pes2704
 */
class CompanyControler extends FrontControlerAbstract {

    /**
     * @var CompanyContactRepoInterface
     */
    private $companyContactRepo;

    /**
     * @var CompanyAddressRepoInterface $companyAddressRepo
     */
     private $companyAddressRepo;     
     
    /**
     * @var CompanyInfoRepoInterface $companyInfoRepo
     */
     private $companyInfoRepo;     
     
     /**
     * @var CompanyRepoInterface
     */
    private $companyRepo;
    /**
     * @var RepresentativeRepoInterface
     */
    private $representativeRepo;    
    
    /**
     * 
     * @var CompanytoNetworkRepoInterface
     */
    private $companyToNetworkRepo;

    /**
     * 
     * @var NetworkRepoInterface
     */
    private $networkRepo;

    /**
     * 
     * @param StatusSecurityRepo $statusSecurityRepo
     * @param StatusFlashRepo $statusFlashRepo
     * @param StatusPresentationRepo $statusPresentationRepo
     * @param CompanyRepoInterface $companyRepo
     * @param CompanyContactRepoInterface $companyContactRepo
     * @param CompanyAddressRepoInterface $companyAddressRepo
     * @param RepresentativeRepoInterface $representativeRepo
     */
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            
            CompanyRepoInterface $companyRepo,
            CompanyContactRepoInterface $companyContactRepo,
            CompanyAddressRepoInterface $companyAddressRepo,
            CompanyInfoRepoInterface $companyInfoRepo,
            RepresentativeRepoInterface $representativeRepo,
            CompanytoNetworkRepoInterface $companyToNetworkRepo,
            NetworkRepoInterface $networkRepo
            
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->companyRepo = $companyRepo;
        $this->companyContactRepo = $companyContactRepo;
        $this->companyAddressRepo = $companyAddressRepo;
        $this->companyInfoRepo = $companyInfoRepo;
        $this->representativeRepo = $representativeRepo;
        $this->companyToNetworkRepo = $companyToNetworkRepo;
        $this->networkRepo = $networkRepo;
    }
    
    
    #### private ####
    
    private function getStatusLoginAggregate(): ?LoginAggregateFullInterface {
        /** @var StatusSecurityRepo $statusSecurityRepo */
        $statusSecurity = $this->statusSecurityRepo->get();
        /** @var LoginAggregateFullInterface $loginAggregateCredentials */
        return $statusSecurity->getLoginAggregate();        
    }
  
    //TODO: permissions jen pokud je zapnuta editace, oddělit administratora - pro add update remove Company
    private function hasPermissions(LoginAggregateFullInterface $loginAggregateCredentials, $companyId) {
        $loginName = $loginAggregateCredentials->getLoginName();            
        $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? '';         
        $isRepresentative = false;
        if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative']) ) {               
            if ( $this->representativeRepo->get($loginName, $companyId ) )   {
                $isRepresentative = true; 
            }
        }            
        return ( $isRepresentative OR ($role ==  ConfigurationCache::auth()['roleEventsAdministrator']) );      
    }
    
    private function hasAdminPermissions(LoginAggregateFullInterface $loginAggregateCredentials) {
        $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? '';         
        return (isset($role) AND ($role ==  ConfigurationCache::auth()['roleEventsAdministrator']) );            
    }
    
    private function checkParentId($companyId) {
        if (null===$this->companyRepo->get($companyId)) {
            throw new UnexpectedValueException("Invalid path. Id mismatch.");
        }
        return $companyId;
    }


    #### public ####
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @return type
     */
    public function addCompany (ServerRequestInterface $request) {                 
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            /** @var CompanyInterface $company */                        
            $company = new Company();
            $company->setName( (new RequestParams())->getParsedBodyParam($request, 'name') );                
            $this->companyRepo->add($company);
        } else {
            $this->addFlashMessage("Nemáte oprávnění.");
        }                           
        return $this->redirectSeeLastGet($request);
    }
    
    
  
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $idCompany
     * @return type
     */
    public function updateCompany (ServerRequestInterface $request, $idCompany) {                   
    if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            /** @var CompanyInterface $company */
            $company = $this->companyRepo->get( $idCompany );                
            // POST formularovadata
            $company->setName( (new RequestParams())->getParsedBodyParam($request, 'name') );                
        } else {
            $this->addFlashMessage("Nemáte oprávnění.");
        }            
        return $this->redirectSeeLastGet($request);
    }
    
        
       
    
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $idCompany
     * @return type
     */
    public function removeCompany (ServerRequestInterface $request,  $idCompany) {  
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {        
            /** @var CompanyInterface $company */
            $company = $this->companyRepo->get( $idCompany );
            $this->companyRepo->remove( $company ); 

        } else {
            $this->addFlashMessage("Nemáte oprávnění.");
        }            
        return $this->redirectSeeLastGet($request);
    }
            
    
    
    
     
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $idCompany
     * @return type
     */
    public function addCompanyContact (ServerRequestInterface $request, $idCompany) {                 
        $loginAggregateCredentials = $this->getStatusLoginAggregate();
        if (!isset($loginAggregateCredentials)) {
            return $this->createUnauthorizedResponse();
        }                                  
        if ($this->hasPermissions($loginAggregateCredentials, $idCompany)) {      
            /** @var CompanyContactInterface $companyContact */
            $companyContact = new CompanyContact(); //new $companyContact
            $companyContact->setCompanyId($idCompany);
            $this->hydrateCompanyContact($request, $companyContact);
            $this->companyContactRepo->add($companyContact);

        } else {
            $this->addFlashMessage("Nemáte oprávnění.");
        }
        return $this->redirectSeeLastGet($request);
    }

    
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $idCompany
     * @param type $idCompanyContact
     * @return type
     */
    public function updateCompanyContact (ServerRequestInterface $request, $idCompany, $idCompanyContact) {                   
        $loginAggregateCredentials = $this->getStatusLoginAggregate();
        if (!isset($loginAggregateCredentials)) {
            return $this->createUnauthorizedResponse();
        }                                  
        if ($this->hasPermissions($loginAggregateCredentials, $idCompany)) {
            /** @var CompanyContactInterface $companyContact */
            $companyContact = $this->companyContactRepo->get( $idCompanyContact );
            $this->hydrateCompanyContact($request, $companyContact);
        } else {
            $this->addFlashMessage("Nemáte oprávnění.");
        }
        return $this->redirectSeeLastGet($request);
    }

    private function hydrateCompanyContact(ServerRequestInterface $request, CompanyContactInterface $companyContact) {
        $requestParams = new RequestParams();
        $companyContact->setName($requestParams->getParsedBodyParam($request, 'name') );
        $companyContact->setPhones($requestParams->getParsedBodyParam($request, 'phones'));
        $companyContact->setMobiles($requestParams->getParsedBodyParam($request, "mobiles"));
        $companyContact->setEmails($requestParams->getParsedBodyParam($request, "emails"));               
    }
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $idCompany
     * @param type $idCompanyContact
     * @return type
     */
    public function removeCompanyContact (ServerRequestInterface $request,  $idCompany, $idCompanyContact) {                   
        $loginAggregateCredentials = $this->getStatusLoginAggregate();
        if (!isset($loginAggregateCredentials)) {
            return $this->createUnauthorizedResponse();
        }                                  
        if ($this->hasPermissions($loginAggregateCredentials, $idCompany)) {        
            /** @var CompanyContactInterface $companyContact */
            $companyContact = $this->companyContactRepo->get( $idCompanyContact );
            $this->companyContactRepo->remove( $companyContact ); 

        } else {
            $this->addFlashMessage("Nemáte oprávnění.");
        }
        return $this->redirectSeeLastGet($request);
    }
            
    
//------------------------------------------------------------------------------------------------
     
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $idCompany
     * @return type
     */
    public function addCompanyAddress (ServerRequestInterface $request, $idCompany) {                 
        $loginAggregateCredentials = $this->getStatusLoginAggregate();
        if (!isset($loginAggregateCredentials)) {
            return $this->createUnauthorizedResponse();
        }                                  
        if ($this->hasPermissions($loginAggregateCredentials, $idCompany)) {
            /** @var CompanyAddressInterface $companyAddress */
            $companyAddress =  new CompanyAddress(); //new 
            $companyAddress->setCompanyId($idCompany);
            $this->hydrateCompanyAddress($request, $companyAddress);
            $this->companyAddressRepo->add($companyAddress);

        } else {
            $this->addFlashMessage("Nemáte oprávnění.");
        }
        return $this->redirectSeeLastGet($request);
    }
    
    
    
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $idCompany
     * @param type $idCompanyA
     * @return type
     */
    public function updateCompanyAddress (ServerRequestInterface $request, $idCompany, $idCompanyA) {                   
        $loginAggregateCredentials = $this->getStatusLoginAggregate();
        if (!isset($loginAggregateCredentials)) {
            return $this->createUnauthorizedResponse();
        }                                  
        if ($this->hasPermissions($loginAggregateCredentials, $idCompany)) {              
            /** @var CompanyAddressInterface $companyAddress */
            $companyAddress = $this->companyAddressRepo->get( $idCompanyA );  
            if (!isset($companyAddress)) {
                throw new UnexpectedValueException("Invalid path. Invalid child id.");
            }
            $this->checkParentId($companyAddress->getCompanyId());
            $this->hydrateCompanyAddress($request, $companyAddress);
        } else {
            $this->addFlashMessage("Nemáte oprávnění.");
        }
        return $this->redirectSeeLastGet($request);
    }
    
    private function hydrateCompanyAddress(ServerRequestInterface $request, CompanyAddressInterface $companyAddress) {
        $requestParams = new RequestParams();
        $companyAddress->setName($requestParams->getParsedBodyParam($request, 'name') );
        $companyAddress->setLokace($requestParams->getParsedBodyParam($request, 'lokace'));
        $companyAddress->setPsc($requestParams->getParsedBodyParam($request, "psc"));
        $companyAddress->setObec($requestParams->getParsedBodyParam($request, "obec"));           
    }    
    
   
    /**
     * POST company address
     * 
     * @param ServerRequestInterface $request
     * @param type $idCompany
     * @param type $idCompanyA
     * @return type
     */
    public function removeCompanyAddress (ServerRequestInterface $request, $idCompany, $idCompanyA)  {                   
        $loginAggregateCredentials = $this->getStatusLoginAggregate();
        if (!isset($loginAggregateCredentials)) {
            return $this->createUnauthorizedResponse();
        }                                  
        if ($this->hasPermissions($loginAggregateCredentials, $idCompany)) {     
            /** @var CompanyAddressIntarface $companyAddress */
            $companyAddress = $this->companyAddressRepo->get( $idCompany ); 
            if (!isset($companyAddress)) {
                throw new UnexpectedValueException("Invalid path. Invalid child id.");
            }
            $this->checkParentId($companyAddress->getCompanyId());            
            $this->companyAddressRepo->remove( $companyAddress ); 

        } else {
            $this->addFlashMessage("Nemáte oprávnění.");
        }
        return $this->redirectSeeLastGet($request);
    }
    
    public function addCompanyInfo(ServerRequestInterface $request, $idCompany) {
        $loginAggregateCredentials = $this->getStatusLoginAggregate();
        if (!isset($loginAggregateCredentials)) {
            return $this->createUnauthorizedResponse();
        }                                  
        if ($this->hasPermissions($loginAggregateCredentials, $idCompany)) {
            /** @var CompanyInfoInterface $companyInfo */
            $companyInfo =  new CompanyInfo();
            $companyInfo->setCompanyId($idCompany);
            $this->hydrateCompanyInfo($request, $companyInfo);
            $this->companyInfoRepo->add($companyInfo);

        } else {
            $this->addFlashMessage("Nemáte oprávnění.");
        }
        return $this->redirectSeeLastGet($request);        
    }
    
    public function updateCompanyInfo(ServerRequestInterface $request, $idCompany, $idCompanyA) {
        $loginAggregateCredentials = $this->getStatusLoginAggregate();
        if (!isset($loginAggregateCredentials)) {
            return $this->createUnauthorizedResponse();
        }                                  
        if ($this->hasPermissions($loginAggregateCredentials, $idCompany)) {              
            /** @var CompanyInfoInterface $companyInfo */
            $companyInfo = $this->companyInfoRepo->get( $idCompanyA );  
            if (!isset($companyInfo)) {
                throw new UnexpectedValueException("Invalid path. Invalid child id.");
            }
            $this->hydrateCompanyInfo($request, $companyInfo);
            $this->processCompanyToNetwork($request, $idCompany);
        } else {
            $this->addFlashMessage("Nemáte oprávnění.");
        }
        return $this->redirectSeeLastGet($request);        
    }
    
    private function hydrateCompanyInfo(ServerRequestInterface $request, CompanyInfoInterface $companyInfo) {
        $requestParams = new RequestParams();
        $companyInfo->setIntroduction(substr($requestParams->getParsedBodyParam($request, 'introduction') ), 0, 1000);
        $companyInfo->setVideoLink($requestParams->getParsedBodyParam($request, 'videolink'));
        $companyInfo->setPositives(substr($requestParams->getParsedBodyParam($request, "positives")), 0, 1000);
        $companyInfo->setSocial(substr($requestParams->getParsedBodyParam($request, "social")), 0, 1000);    
    }    
    
    public function removeCompanyInfo(ServerRequestInterface $request, $idCompany, $idCompanyA) {
        $loginAggregateCredentials = $this->getStatusLoginAggregate();
        if (!isset($loginAggregateCredentials)) {
            return $this->createUnauthorizedResponse();
        }                                  
        if ($this->hasPermissions($loginAggregateCredentials, $idCompany)) {     
            /** @var CompanyInfoInterface $companyInfo */
            $companyInfo = $this->companyInfoRepo->get( $idCompanyA );  
            if (!isset($companyInfo)) {
                throw new UnexpectedValueException("Invalid path. Invalid child id.");
            }           
            $this->companyInfoRepo->remove( $companyInfo ); 

        } else {
            $this->addFlashMessage("Nemáte oprávnění.");
        }
        return $this->redirectSeeLastGet($request);        
    }
    
    
    private function processCompanyToNetwork(ServerRequestInterface $request, $companyId) {
        $companyNetworksIds = [];
        $companyToNetworks = $this->companyToNetworkRepo->findByCompanyId($companyId);
        /** @var JobToTagInterface $companyToNetwork */
        foreach ($companyToNetworks as $companyToNetwork) {   
            $companyNetworksIds[] = $companyToNetwork->getNetworkId() ; 
        }                                
        $allNetworks = $this->networkRepo->findAll(); 
        $data = (new RequestParams())->getParsedBodyParam($request, "data" );  // když není žádný checkbox zaškrtnut => nejsou POST data => $data=null
        $links = (new RequestParams())->getParsedBodyParam($request, "link" );  // když není žádný checkbox zaškrtnut => nejsou POST data => $link=null
        
        /** @var NetworkInterface $network */
        foreach ($allNetworks as $network) {
            $companyToNetwork = $this->companyToNetworkRepo->get($companyId, $network->getId());
            
            // $postNetworkId - tento tag je zaskrtnut ve form
            $postChecked = $this->dataParam($data, $network->getIcon(), false) ? 1 : 0;  // key=icon, value=id network 
            $postLink = $this->dataParam($links, $network->getId());    // key = id network, value= link
            
            if ($postChecked OR (isset($postLink) AND $postLink)) { // je zaskrtnut ve form nebo má hodnotu link (neprázdný řetězec)
                if (!isset($companyToNetwork)) {                                                                            
                    /** @var CompanytoNetworkInterface $companyToNetwork */
                    $companyToNetwork = new CompanytoNetwork(); //new 
                    $companyToNetwork->setCompanyId($companyId); 
                    $companyToNetwork->setNetworkId($network->getId());
                    $companyToNetwork->setLink($postLink);
                    $companyToNetwork->setPublished($postChecked);
                    $this->companyToNetworkRepo->add($companyToNetwork);
                } else {
                    $companyToNetwork->setLink($postLink);
                    $companyToNetwork->setPublished($postChecked);
                }                    
            } else { // neni zaskrtnut ve form a nemá hodnotu link                                    
                if (isset($companyToNetwork)) {
                    $this->companyToNetworkRepo->remove($companyToNetwork);
                }
            }                 
        }
    }    
    
    private function dataParam($data, $key, $default = null) {
        return (isset($data) AND array_key_exists($key, $data)) ? $data[$key] : $default ;
    }
    //---------------------------------------------------------------------------------
    // TODO: SV - permissions!!
    
     public function addRepresentative (ServerRequestInterface $request) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {         
            // POST data                
            $selectCompanyId = (new RequestParams())->getParsedBodyParam($request, "selectCompany");
            $selectLogin = (new RequestParams())->getParsedBodyParam($request, "selectLogin");

            /** @var RepresentativeInterface $representative */
            $representative = new Representative(); //new              
            $representative->setCompanyId($selectCompanyId) ;
            $representative->setLoginLoginName($selectLogin);

            $this->representativeRepo->add($representative);                
            //------------------------------------------------------------------------------------

            /** @var CompanyInterface $companyEntity */
            $companyEntity = $this->companyRepo->get($selectCompanyId);
            $companyName = $companyEntity->getName();

            /*  company name, login name , tady neznam mail adr.- musim jinam */
            $ret = $this->sendMailUsingAuthData($request, $companyName, $selectLogin);   
            if (isset($ret)) {
                $this->addFlashMessage("Mail o dokončení registrace odeslán." );
            }
        } else {
            $this->addFlashMessage("Nemáte oprávnění.");
        }
        return $this->redirectSeeLastGet($request);
    }


    private function sendMailUsingAuthData (ServerRequestInterface $request, $companyName, $loginName){
        $scheme = $request->getUri()->getScheme();
        $host = $request->getUri()->getHost();

        $ruri = $this->getUriInfo($request)->getRestUri();
        $rap =$this->getUriInfo($request)->getRootAbsolutePath();
        $sp = $this->getUriInfo($request)->getSubdomainPath();
        $url = "$scheme://$host$sp"."auth/v1/mailCompletRegistrationRepre";
        $data = ['companyName' => $companyName, 'loginName' => $loginName ];

        // options pro stream_context_create() vždy definuj s položkou http
        // url adresu pro file_get_contents(url, ..) definuj: https://....
        // use key 'http' even if you send the request to https://...
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded",
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === false) {
            $this->addFlashMessage("Mail o dokončení registrace se nepodařilo odeslat.", FlashSeverityEnum::WARNING);
        } else {
            return true ;     
        }
        
####        
//        $context = stream_context_create($options);
//        $result = file_get_contents($url, false, $context);
//        if ($result === false) {
//            $this->addFlashMessage("Není spojení", FlashSeverityEnum::WARNING);
//        } else {
//            
//            $str1=$str2=$status=null;
//            sscanf($http_response_header[0] ,'%s %d %s', $str1,$status, $str2);
//            if($status==200) {
//                return $result;        
//            } else {
//                // response s jiným stavem než 200 OK
//                    throw new \Exception($http_response_header[0]);
//            }
//        }

    } 
    
    
    
    public function removeRepresentative (ServerRequestInterface $request, $loginLoginName, $companyId ) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
                // POST data                
                /** @var RepresentativeIntarface $representative */
                $representative = $this->representativeRepo->get( $loginLoginName, $companyId ); 
                $this->representativeRepo->remove( $representative ); 
        } else {
            $this->addFlashMessage("Nemáte oprávnění.");
        }
        return $this->redirectSeeLastGet($request);
    }
    
    
   
}
