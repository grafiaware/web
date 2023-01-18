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
use Events\Model\Entity\CompanyContactInterface;
use Events\Model\Entity\CompanyContact;
use Events\Model\Entity\CompanyAddressInterface;
use Events\Model\Entity\CompanyAddress;
use Events\Model\Entity\RepresentativeInterface;
use Events\Model\Entity\Representative;


use Status\Model\Enum\FlashSeverityEnum;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Pes\Http\Helper\RequestStatus;
use Pes\Http\Request\RequestParams;
use Pes\Http\Factory\ResponseFactory;
use Pes\Http\Response;

use Mail\Mail;
use Mail\MessageFactory\HtmlMessage;
use Mail\Params;
use Mail\Params\Content;
use Mail\Params\Attachment; 
use Mail\Params\StringAttachment;
use Mail\Params\Party;


/**
 * Description of NestedFilesUpload
 *
 * @author pes2704
 */
class CompanyControler extends FrontControlerAbstract {

    /**
     * 
     * @var CompanyContactRepoInterface
     */
    private $companyContactRepo;
    /**
     * @var CompanyAddressRepoInterface $companyAddressRepo
     */
     private $companyAddressRepo;
     
    /**
     * 
     * @var CompanyRepoInterface
     */
    private $companyRepo;
    /**
     * 
     * @var RepresentativeRepoInterface
     */
    private $representativeRepo;    
    
        
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
            RepresentativeRepoInterface $representativeRepo
            
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->companyRepo = $companyRepo;
        $this->companyContactRepo = $companyContactRepo;
        $this->companyAddressRepo = $companyAddressRepo;
        $this->representativeRepo = $representativeRepo;
    }
    
    
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $idCompany
     * @return type
     */
    public function addCompanyContact (ServerRequestInterface $request, $idCompany) {                 
        $isRepresentative = false;
        
        /** @var StatusSecurityRepo $statusSecurityRepo */
        $statusSecurity = $this->statusSecurityRepo->get();
        /** @var LoginAggregateFullInterface $loginAggregateCredentials */
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();                           
        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse();
            return $response->withStatus(401);  // Unaathorized
        } else {  
            $loginName = $loginAggregateCredentials->getLoginName();            
            $role = $loginAggregateCredentials->getCredentials()->getRole() ?? ''; 
            
            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) 
                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
                $isRepresentative = true; 
            }
                        
            if ($isRepresentative) {
                // POST formularovadata
//                $name = (new RequestParams())->getParsedBodyParam($request, 'name');               
//                $phones = (new RequestParams())->getParsedBodyParam($request, 'phones');
//                $mobiles = (new RequestParams())->getParsedBodyParam($request, "mobiles");
//                $emails = (new RequestParams())->getParsedBodyParam($request, "emails");
                
                /** @var CompanyContactInterface $companyContact */
                $companyContact = $this->container->get(CompanyContact::class); //new $companyContact
                
                $companyContact->setCompanyId($idCompany);
                $companyContact->setName( (new RequestParams())->getParsedBodyParam($request, 'name') );
                $companyContact->setPhones((new RequestParams())->getParsedBodyParam($request, 'phones'));
                $companyContact->setMobiles((new RequestParams())->getParsedBodyParam($request, "mobiles"));
                $companyContact->setEmails((new RequestParams())->getParsedBodyParam($request, "emails"));
                
                $this->companyContactRepo->add($companyContact);
                
            } else {
                $this->addFlashMessage("Údaje o kontaktech vyvstavovatele smí editovat pouze representant vystavovatele.");
            }
            
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
        $isRepresentative = false;
        
        /** @var StatusSecurityRepo $statusSecurityRepo */
        $statusSecurity = $this->statusSecurityRepo->get();
        /** @var LoginAggregateFullInterface $loginAggregateCredentials */
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();                           
        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse();
            return $response->withStatus(401);  // Unauthorized
        } else {                                   
            $loginName = $loginAggregateCredentials->getLoginName();            
            $role = $loginAggregateCredentials->getCredentials()->getRole() ?? '';         
            
            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) ) {               
                if ( $this->representativeRepo->get($loginName, $idCompany ) )   {
                            $isRepresentative = true; 
                }
            }            
            if ($isRepresentative) {
                /** @var CompanyContactInterface $companyContact */
                $companyContact = $this->companyContactRepo->get( $idCompanyContact );
                
                // POST formularova data                                              
                $companyContact->setName( (new RequestParams())->getParsedBodyParam($request, 'name') );
                $companyContact->setPhones((new RequestParams())->getParsedBodyParam($request, 'phones'));
                $companyContact->setMobiles((new RequestParams())->getParsedBodyParam($request, "mobiles"));
                $companyContact->setEmails((new RequestParams())->getParsedBodyParam($request, "emails"));
                
                $this->companyContactRepo->add($companyContact);
                
            } else {
                $this->addFlashMessage("Údaje o kontaktech vystavovatele smí editovat pouze representant vystavovatele.");
            }
            
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
    public function removeCompanyContact (ServerRequestInterface $request,  $idCompany, $idCompanyContact) {                   
        $isRepresentative = false;
                
        /** @var StatusSecurityRepo $statusSecurityRepo */
        $statusSecurity = $this->statusSecurityRepo->get();
        /** @var LoginAggregateFullInterface $loginAggregateCredentials */
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();                           
        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse();
            return $response->withStatus(401);  // Unaathorized
        } else {                                   
            $loginName = $loginAggregateCredentials->getLoginName();            
            $role = $loginAggregateCredentials->getCredentials()->getRole() ?? '';           
            
            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) ) {
               
                if ( $this->representativeRepo->get($loginName, $idCompany ) )   {
                            $isRepresentative = true; 
                }
            }          
                
            if ($isRepresentative) {                                
                 /** @var CompanyContactInterface $companyContact */
                $companyContact = $this->companyContactRepo->get( $idCompanyContact );
                $this->companyContactRepo->remove( $companyContact ); 
                                
            } else {
                $this->addFlashMessage("Údaje o kontaktech vystavovatele smí mazat pouze representant vystavovatele.");
            }
            
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
        $isRepresentative = false;
           
        /** @var StatusSecurityRepo $statusSecurityRepo */
        $statusSecurity = $this->statusSecurityRepo->get();
        /** @var LoginAggregateFullInterface $loginAggregateCredentials */
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();                           
        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse();
            return $response->withStatus(401);  // Unaathorized
        } else {  
            $loginName = $loginAggregateCredentials->getLoginName();            
            $role = $loginAggregateCredentials->getCredentials()->getRole() ?? ''; 
            
            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) 
                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
                $isRepresentative = true; 
            }
     
            if ($isRepresentative) {
                // POST data
//                $сompanyId = (new RequestParams())->getParsedBodyParam($request, 'company-id');                                       
//                $name = (new RequestParams())->getParsedBodyParam($request, 'name');               
//                $lokace = (new RequestParams())->getParsedBodyParam($request, 'lokace');
//                $psc = (new RequestParams())->getParsedBodyParam($request, "psc");
//                $obec = (new RequestParams())->getParsedBodyParam($request, "obec");
                
                /** @var CompanyAddressInterface $companyAddress */
                $companyAddress = $this->container->get(CompanyAddress::class); //new 
                
                $companyAddress->setCompanyId($idCompany);
                $companyAddress->setName( (new RequestParams())->getParsedBodyParam($request, 'name') );
                $companyAddress->setLokace((new RequestParams())->getParsedBodyParam($request, 'lokace'));
                $companyAddress->setPsc((new RequestParams())->getParsedBodyParam($request, "psc"));
                $companyAddress->setObec((new RequestParams())->getParsedBodyParam($request, "obec"));
                
                $this->companyAddressRepo->add($companyAddress);
                
            } else {
                $this->addFlashMessage("Údaje o adrese vyvstavovatele smí editovat pouze representant vystavovatele.");
            }
            
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
        $isRepresentative = false;
                
         /** @var StatusSecurityRepo $statusSecurityRepo */
        $statusSecurity = $this->statusSecurityRepo->get();
        /** @var LoginAggregateFullInterface $loginAggregateCredentials */
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();                           
        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse();
            return $response->withStatus(401);  // Unauthorized
        } else {                                   
            $loginName = $loginAggregateCredentials->getLoginName();            
            $role = $loginAggregateCredentials->getCredentials()->getRole() ?? '';         
            
            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) ) {              
                if ( $this->representativeRepo->get($loginName, $idCompany ) )   {
                            $isRepresentative = true; 
                }
            }                   
  
            if ($isRepresentative) {                
                /** @var CompanyAddressInterface $companyAddress */
                $companyAddress = $this->companyAddressRepo->get( $idCompany );  
                
                $companyAddress->setName( (new RequestParams())->getParsedBodyParam($request, 'name') );
                $companyAddress->setLokace((new RequestParams())->getParsedBodyParam($request, 'lokace'));
                $companyAddress->setPsc((new RequestParams())->getParsedBodyParam($request, "psc"));
                $companyAddress->setObec((new RequestParams())->getParsedBodyParam($request, "obec"));
                
                $this->companyAddressRepo->add($companyAddress);
                
            } else {
                $this->addFlashMessage("Údaje o kontaktech vystavovatele smí editovat pouze representant vystavovatele.");
            }
            
        }
        return $this->redirectSeeLastGet($request);
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
        $isRepresentative = false;
                        
         /** @var StatusSecurityRepo $statusSecurityRepo */
        $statusSecurity = $this->statusSecurityRepo->get();
        /** @var LoginAggregateFullInterface $loginAggregateCredentials */
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();                           
        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse();
            return $response->withStatus(401);  // Unaathorized
        } else {                                   
            $loginName = $loginAggregateCredentials->getLoginName();            
            $role = $loginAggregateCredentials->getCredentials()->getRole() ?? '';         
            
            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) ) {              
                if ( $this->representativeRepo->get($loginName, $idCompany ) )   {
                            $isRepresentative = true; 
                }
            }                             
                  
            if ($isRepresentative) {                                 
                /** @var CompanyAddressIntarface $companyAddress */
                $companyAddress = $this->companyAddressRepo->get( $idCompany ); 
                $this->companyAddressRepo->remove( $companyAddress ); 
                                
            } else {
                $this->addFlashMessage("Údaje o kontaktech vystavovatele smí mazat pouze representant vystavovatele.");
            }
            
        }
        return $this->redirectSeeLastGet($request);
    }
    
    
    //---------------------------------------------------------------------------------
    
    
     public function addRepresentative (ServerRequestInterface $request) {                 
//        $isRepresentative = false;
//        
//        /** @var StatusSecurityRepo $statusSecurityRepo */
//        $statusSecurity = $this->statusSecurityRepo->get();
//        /** @var LoginAggregateFullInterface $loginAggregateCredentials */
//        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();                           
//        if (!isset($loginAggregateCredentials)) {
//            $response = (new ResponseFactory())->createResponse();
//            return $response->withStatus(401);  // Unaathorized
//        } else {  
//            $loginName = $loginAggregateCredentials->getLoginName();            
//            $role = $loginAggregateCredentials->getCredentials()->getRole() ?? ''; 
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName) )  {
//                $isRepresentative = true; 
//            }
//                        
//            if ($isRepresentative) {
              
         
                // POST data                
                $selectCompany = (new RequestParams())->getParsedBodyParam($request, "selectCompany");
                $selectLogin = (new RequestParams())->getParsedBodyParam($request, "selectLogin");
                
                /** @var RepresentativeInterface $representative */
                $representative = $this->container->get(Representative::class); //new              
                $representative->setCompanyId((new RequestParams())->getParsedBodyParam($request, 'selectCompany') ) ;
                $representative->setLoginLoginName( (new RequestParams())->getParsedBodyParam($request, 'selectLogin') );
                                
                $this->representativeRepo->add($representative);
                
//            } else {
//                $this->addFlashMessage("Údaje o adrese vyvstavovatele smí editovat pouze representant vystavovatele.");
//            }
//            
//        }
        return $this->redirectSeeLastGet($request);
    }
    
    
    
    public function removeRepresentative (ServerRequestInterface $request, $loginLoginName, $companyId ) {                   
//        $isRepresentative = false;
//        
//        /** @var StatusSecurityRepo $statusSecurityRepo */
//        $statusSecurity = $this->statusSecurityRepo->get();
//        /** @var LoginAggregateFullInterface $loginAggregateCredentials */
//        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();                           
//        if (!isset($loginAggregateCredentials)) {
//            $response = (new ResponseFactory())->createResponse();
//            return $response->withStatus(401);  // Unaathorized
//        } else {
//            
//            $loginName = $loginAggregateCredentials->getLoginName();            
//            $role = $loginAggregateCredentials->getCredentials()->getRole() ?? ''; 
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName) )  {
//                $isRepresentative = true; 
//            }
//                        
//            if ($isRepresentative) {
        
        
                // POST data                
                /** @var RepresentativeIntarface $representative */
                $representative = $this->representativeRepo->get( $loginLoginName, $companyId ); 
                $this->representativeRepo->remove( $representative ); 

                
//            } else {
//                $this->addFlashMessage("Údaje o kontaktech vystavovatele smí mazat pouze representant vystavovatele.");
//            }
//            
//        }
        return $this->redirectSeeLastGet($request);
    }
    
    
   
}