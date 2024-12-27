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

use Events\Model\Entity\CompanyInterface;
use Events\Model\Entity\Company;
use Events\Model\Entity\CompanyContactInterface;
use Events\Model\Entity\CompanyContact;
use Events\Model\Entity\CompanyAddressInterface;
use Events\Model\Entity\CompanyAddress;
use Events\Model\Entity\RepresentativeInterface;
use Events\Model\Entity\Representative;

use Mail\Mail;
use Mail\MessageFactory\HtmlMessage;
use Mail\Params;
use Mail\Params\{Content, Attachment, Party};

use Status\Model\Enum\FlashSeverityEnum;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Pes\Http\Helper\RequestStatus;
use Pes\Http\Request\RequestParams;
use Pes\Http\Factory\ResponseFactory;
use Pes\Http\Response;


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
     * @var CompanyRepoInterface
     */
    private $companyRepo;
    /**
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
     * @return type
     */
    public function addCompany (ServerRequestInterface $request) {                 
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
//            $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? ''; 
//            
//            if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
//                $isRepresentative = true; 
//            }
//                        
//            if ($isRepresentative) {
            
                /** @var CompanyInterface $company */                        
                $company = new Company();//new $company 
                // POST formularovadata
                $company->setName( (new RequestParams())->getParsedBodyParam($request, 'name') );                
                $this->companyRepo->add($company);
                
//            } else {
//                $this->addFlashMessage("Údaje o ...e smí editovat pouze representant vystavovatele.");
//            }           
//        }
                
        return $this->redirectSeeLastGet($request);
    }
    
    
  
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $idCompany
     * @return type
     */
    public function updateCompany (ServerRequestInterface $request, $idCompany) {                   
//        $isRepresentative = false;
//        
//        /** @var StatusSecurityRepo $statusSecurityRepo */
//        $statusSecurity = $this->statusSecurityRepo->get();
//        /** @var LoginAggregateFullInterface $loginAggregateCredentials */
//        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();                           
//        if (!isset($loginAggregateCredentials)) {
//            $response = (new ResponseFactory())->createResponse();
//            return $response->withStatus(401);  // Unauthorized
//        } else {                                   
//            $loginName = $loginAggregateCredentials->getLoginName();            
//            $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? '';         
//            
//            if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative']) ) {               
//                if ( $this->representativeRepo->get($loginName, $idCompany ) )   {
//                            $isRepresentative = true; 
//                }
//            }                      
//            if ($isRepresentative) {
        
        
                /** @var CompanyInterface $company */
                $company = $this->companyRepo->get( $idCompany );                
                // POST formularovadata
                $company->setName( (new RequestParams())->getParsedBodyParam($request, 'name') );                
//            } else {
//                $this->addFlashMessage("Údaje o ... smí editovat pouze representant vystavovatele.");
//            }            
//        }
        return $this->redirectSeeLastGet($request);
    }
    
        
       
    
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $idCompany
     * @return type
     */
    public function removeCompany (ServerRequestInterface $request,  $idCompany) {                   
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
//            $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? '';           
//            
//            if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative']) ) {
//               
//                if ( $this->representativeRepo->get($loginName, $idCompany ) )   {
//                            $isRepresentative = true; 
//                }
//            }          
//            if ($isRepresentative) {                 
        
                 /** @var CompanyInterface $company */
                $company = $this->companyRepo->get( $idCompany );
                $this->companyRepo->remove( $company ); 
                                
//            } else {
//                $this->addFlashMessage("Údaje o ... vystavovatele smí mazat pouze representant vystavovatele.");
//            }            
//        }
        return $this->redirectSeeLastGet($request);
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
            $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? ''; 
            
            if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative']) 
                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
                $isRepresentative = true; 
            }
                        
        if ( ($isRepresentative) OR ($role ==  ConfigurationCache::auth()['roleEventsAdministrator']) ) {         
                /** @var CompanyContactInterface $companyContact */
                $companyContact = new CompanyContact(); //new $companyContact
                
                // POST formularova data
                $companyContact->setCompanyId($idCompany);
                $companyContact->setName( (new RequestParams())->getParsedBodyParam($request, 'name') );
                $companyContact->setPhones((new RequestParams())->getParsedBodyParam($request, 'phones'));
                $companyContact->setMobiles((new RequestParams())->getParsedBodyParam($request, "mobiles"));
                $companyContact->setEmails((new RequestParams())->getParsedBodyParam($request, "emails"));
                
                $this->companyContactRepo->add($companyContact);
                
            } else {
                $this->addFlashMessage("Údaje o kontaktech firmy smí editovat pouze representant firmy (popř. administrator).");
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
            $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? '';         
            
            if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative']) ) {               
                if ( $this->representativeRepo->get($loginName, $idCompany ) )   {
                            $isRepresentative = true; 
                }
            }            
        if ( ($isRepresentative) OR ($role ==  ConfigurationCache::auth()['roleEventsAdministrator']) ) {          
                /** @var CompanyContactInterface $companyContact */
                $companyContact = $this->companyContactRepo->get( $idCompanyContact );
                
                // POST formularova data                                              
                $companyContact->setName( (new RequestParams())->getParsedBodyParam($request, 'name') );
                $companyContact->setPhones((new RequestParams())->getParsedBodyParam($request, 'phones'));
                $companyContact->setMobiles((new RequestParams())->getParsedBodyParam($request, "mobiles"));
                $companyContact->setEmails((new RequestParams())->getParsedBodyParam($request, "emails"));            
            } else {
                $this->addFlashMessage("Údaje o kontaktech firmy smí editovat pouze representant firmy (popř. administrator).");
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
            $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? '';           
            
            if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative']) ) {               
                if ( $this->representativeRepo->get($loginName, $idCompany ) )   {
                            $isRepresentative = true; 
                }
            }          
                
        if ( ($isRepresentative) OR ($role ==  ConfigurationCache::auth()['roleEventsAdministrator']) ) {           
                /** @var CompanyContactInterface $companyContact */
                $companyContact = $this->companyContactRepo->get( $idCompanyContact );
                $this->companyContactRepo->remove( $companyContact ); 
                                
            } else {
                $this->addFlashMessage("Údaje o kontaktech firmy smí mazat pouze representant firmy (popř. administrator).");
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
            $role = $loginAggregateCredentials->getCredentials()->getRoleFk(); 
            
            if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative']) 
                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
                $isRepresentative = true; 
            }
     
            if ( ($isRepresentative) OR ($role ==  ConfigurationCache::auth()['roleEventsAdministrator']) ) {
                // POST data                
                /** @var CompanyAddressInterface $companyAddress */
                $companyAddress =  new CompanyAddress(); //new 
                
                $companyAddress->setCompanyId($idCompany);
                $companyAddress->setName( (new RequestParams())->getParsedBodyParam($request, 'name') );
                $companyAddress->setLokace((new RequestParams())->getParsedBodyParam($request, 'lokace'));
                $companyAddress->setPsc((new RequestParams())->getParsedBodyParam($request, "psc"));
                $companyAddress->setObec((new RequestParams())->getParsedBodyParam($request, "obec"));
                
                $this->companyAddressRepo->add($companyAddress);
                
            } else {
                $this->addFlashMessage("Údaje o adrese firmy smí editovat pouze representant firmy (popř. administrator).");
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
            $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? '';         
            
            if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative'] ) ) {              
                if ( $this->representativeRepo->get($loginName, $idCompany ) )   {
                            $isRepresentative = true; 
                }
            }                   
  
            if ( ($isRepresentative) OR ( $role ==  ConfigurationCache::auth()[ 'roleEventsAdministrator' ] ) )   {                
                /** @var CompanyAddressInterface $companyAddress */
                $companyAddress = $this->companyAddressRepo->get( $idCompany );  
                if (!isset($companyAddress)) {
                    $companyAddress = new CompanyAddress();
                    $this->companyAddressRepo->add($companyAddress);
                }
                $companyAddress->setName( (new RequestParams())->getParsedBodyParam($request, 'name') );
                $companyAddress->setLokace((new RequestParams())->getParsedBodyParam($request, 'lokace'));
                $companyAddress->setPsc((new RequestParams())->getParsedBodyParam($request, "psc"));
                $companyAddress->setObec((new RequestParams())->getParsedBodyParam($request, "obec"));
                
                
            } else {
                $this->addFlashMessage("Údaje o adrese firmy smí editovat pouze representant firmy (popř. administrator).");
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
            $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? '';         
            
            if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative']) ) {              
                if ( $this->representativeRepo->get($loginName, $idCompany ) )   {
                            $isRepresentative = true; 
                }
            }                             
                  
        if ( ($isRepresentative) OR ($role ==  ConfigurationCache::auth()['roleEventsAdministrator']) ) {           
                /** @var CompanyAddressIntarface $companyAddress */
                $companyAddress = $this->companyAddressRepo->get( $idCompany ); 
                $this->companyAddressRepo->remove( $companyAddress ); 
                                
            } else {
                $this->addFlashMessage("Údaje o adrese firmy smí mazat pouze representant firmy (popř. administrator).");
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
//            $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? ''; 
//            if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName) )  {
//                $isRepresentative = true; 
//            }
//                        
//            if ($isRepresentative) {

         
                // POST data                
                $selectCompany = (new RequestParams())->getParsedBodyParam($request, "selectCompany");
                $selectLogin = (new RequestParams())->getParsedBodyParam($request, "selectLogin");
                
                /** @var RepresentativeInterface $representative */
                $representative = new Representative(); //new              
                $representative->setCompanyId((new RequestParams())->getParsedBodyParam($request, 'selectCompany') ) ;
                $representative->setLoginLoginName( (new RequestParams())->getParsedBodyParam($request, 'selectLogin') );
                                
                $this->representativeRepo->add($representative);
                
                //---------------------------------------------------------------------------------------                                         
                $getInfo = "NAKAFIRMA";    
                $loginNameFk = "REGISTROVANY ZA FIRMU";
                $registerEmail = "selnerova@grafia.cz";
                if ($getInfo) {
                // #########################--------- poslat mail -------------------
                        /** @var Mail $mail */
                        $mail = $this->container->get(Mail::class);
                        /** @var HtmlMessage $mailMessageFactory */
                        $mailMessageFactory = $this->container->get(HtmlMessage::class);

                        $attachments = [ (new Attachment())
//                                        ->setFileName(ConfigurationCache::mail()['mail.attachments'].'logo_grafia.png')
//                                        ->setAltText('Logo Grafia')
                                       ];
                        $data_logo_grafia="data:image/gif;base64,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" ;
                        $subject =  'Veletrh práce a vzdělávání - Registrace dokončena.';
                        
                        $body = $mailMessageFactory->create(__DIR__."/Messages/confirmRepre2.php",
                                                            [  'data_logo_grafia' => $data_logo_grafia,
                                                              // 'logo_grafia'   => 'logo_grafia.png'
                                                            ] );                                                
                        $params = (new Params())
                                    ->setContent(  (new Content())
                                                 ->setSubject($subject)
                                                 ->setHtml($body)
 //                                              ->setAttachments($attachments)
                                                 )
                                    ->setParty  (  (new Party())
                                                 ->setFrom('info@najdisi.cz', 'web najdisi')
                                                 //->addReplyTo('svoboda@grafia.cz', 'info web najdisi'))
                                                 ->addTo( $registerEmail, $loginNameFk)
                                                );
                        $mail->mail($params); // posle mail
                 // #########################----------------              
                }   
                //---------------------------------------------------------------------------------------                                         

                
                
                
                
                
//            } else {
//                $this->addFlashMessage("Údaje o ... vyvstavovatele smí editovat pouze representant vystavovatele.");
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
//            $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? ''; 
//            if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative']) 
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
//                $this->addFlashMessage("Údaje o ... vystavovatele smí mazat pouze representant vystavovatele.");
//            }
//            
//        }
        return $this->redirectSeeLastGet($request);
    }
    
    
   
}
