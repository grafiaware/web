<?php

namespace Events\Middleware\Events\Controler;

use FrontControler\FrontControlerAbstract;
use Psr\Http\Message\ServerRequestInterface;
use Pes\Application\AppFactory;
use Pes\Http\Request\RequestParams;
use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Middleware\Api\Controller\Exception\UnexpectedLanguageException;

use Events\Model\Entity\InstitutionTypeInterface;
use Events\Model\Entity\InstitutionType;
use Events\Model\Repository\InstitutionTypeRepoInterface;

use Events\Model\Entity\InstitutionInterface;
use Events\Model\Entity\Institution;
use Events\Model\Repository\InstitutionRepoInterface;


use Events\Model\Entity\EventContentTypeInterface;
use Events\Model\Entity\EventContentType;
use Events\Model\Repository\EventContentTypeRepoInterface;

use Events\Model\Entity\EventContentInterface;
use Events\Model\Entity\EventContent;
use Events\Model\Repository\EventContentRepoInterface;




//use Events\Model\Arraymodel\Event;
//use Model\Entity\Enroll;


/**
 * 
 */
class EventControler_2 extends FrontControlerAbstract {

    const NULL_VALUE_nahradni = "Toto je speciální hodnota představující NULL";        
        
    /**
     * 
     * @var InstitutionTypeRepoInterface
     */
    private $institutionTypeRepo;   
    /**
     * 
     * @var InstitutionRepoInterface
     */
    private $institutionRepo;
    /**
     * 
     * @var EventContentTypeRepoInterface
     */
    private $eventContentTypeRepo;   
    /**
     * 
     * @var EventContentRepoInterface
     */
    private $eventContentRepo;
    
    
    
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            
            InstitutionRepoInterface  $institutionRepo,
            InstitutionTypeRepoInterface  $institutionTypeRepo,
            EventContentRepoInterface  $eventContentRepo,
            EventContentTypeRepoInterface  $eventContentTypeRepo 
                       
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
       
        $this->institutionRepo = $institutionRepo;
        $this->institutionTypeRepo = $institutionTypeRepo;
        
        $this->eventContentRepo = $eventContentRepo;
        $this->eventContentTypeRepo = $eventContentTypeRepo;
             
    }

    //----------------------------------------------------------------------------------------------
    
  
    
           
    /**
     * 
     * @param ServerRequestInterface $request
     * @return type
     */
    public function addInstitutionType (ServerRequestInterface $request) {                 
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
//            
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
//                $isRepresentative = true; 
//            }                        
//            if ($isRepresentative) {

                
                    /** @var InstitutionTypeInterface $institutionType */
                    //$institutionType = $this->container->get(InstitutionType::class); //new    
                    $institutionType = new InstitutionType(); //new           
                    $institutionType->setInstitutionType((new RequestParams())->getParsedBodyParam($request, 'institutionType') );
    
                    $this->institutionTypeRepo->add($institutionType);             
               
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic smí přidávat pouze ...");
//            }
//        }   
        
        return $this->redirectSeeLastGet($request);
    }
    
    
      
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $institutionTypeId
     * @return type
     */
    public function updateInstitutionType (ServerRequestInterface $request, $institutionTypeId) {                    
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
//            
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
//                $isRepresentative = true; 
//            }                        
//            if ($isRepresentative) {
                           
                /** @var InstitutionTypeInterface $institutionType */
                $institutionType = $this->institutionTypeRepo->get( $institutionTypeId );                      
                $institutionType->setInstitutionType((new RequestParams())->getParsedBodyParam($request, 'institutionType') );                         
        
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic smí editovat pouze ...");
//            }
//        }           
        
        return $this->redirectSeeLastGet($request);

    }    
      

    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $institutionTypeId
     * @return type
     */
    public function removeInstitutionType (ServerRequestInterface $request, $institutionTypeId ) {                   
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
//            
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) ) {               
//                if ( $this->representativeRepo->get($loginName, $idCompany ) )   {
//                            $isRepresentative = true; 
//                }
//            }                          
//            if ($isRepresentative) {                                                    
                
                /** @var InstitutionTypeInterface $institutionType */
                $institutionType = $this->institutionTypeRepo->get($institutionTypeId);    
                $this->institutionTypeRepo->remove($institutionType);                
                                                
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic  smí odstraňovat pouze ....");
//            }           
//        }
                
        return $this->redirectSeeLastGet($request);
    }
            
     
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @return type
     */
    public function addInstitution (ServerRequestInterface $request) {                 
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
//            
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
//                $isRepresentative = true; 
//            }                        
//            if ($isRepresentative) {
                
                /** @var InstitutionInterface $institution */
                //$institution = new Institution(); //new      
                $institution = $this->container->get(InstitutionInterface::class); //new     
                $institution->setName((new RequestParams())->getParsedBodyParam($request, 'institutionName') );
                
                //$institution->setInstitutionTypeId((new RequestParams())->getParsedBodyParam($request, 'selinstitutionTypeId') );
                if ( (new RequestParams())->getParsedBodyParam($request, 'selinstitutionTypeId') != self::NULL_VALUE_nahradni )   {
                      $institution->setInstitutionTypeId ((new RequestParams())->getParsedBodyParam($request, 'selinstitutionTypeId') );
                }     
                            

                $this->institutionRepo->add($institution);             
               
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic smí přidávat pouze ...");
//            }
//        }   
        
        return $this->redirectSeeLastGet($request);
    }
    
    
      
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $institutionId
     * @return type
     */
    public function updateInstitution (ServerRequestInterface $request, $institutionId) {                    
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
//            
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
//                $isRepresentative = true; 
//            }                        
//            if ($isRepresentative) {
            
                /** @var InstitutionInterface $institution */
                $institution = $this->institutionRepo->get($institutionId);             
                $institution->setName((new RequestParams())->getParsedBodyParam($request, 'institutionName') );
                //$institution->setInstitutionTypeId((new RequestParams())->getParsedBodyParam($request, 'selinstitutionTypeId') );     
                
                if ( (new RequestParams())->getParsedBodyParam($request, 'selinstitutionTypeId') != self::NULL_VALUE_nahradni )   {
                    $institution->setInstitutionTypeId ((new RequestParams())->getParsedBodyParam($request, 'selinstitutionTypeId') );
                }     
                else {
                    $institution->setInstitutionTypeId( null );
                }         
        
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic smí editovat pouze ...");
//            }
//        }           
        
        return $this->redirectSeeLastGet($request);

    }    
      
   
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $institutionId
     * @return type
     */
    public function removeInstitution (ServerRequestInterface $request, $institutionId ) {                   
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
//            
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) ) {               
//                if ( $this->representativeRepo->get($loginName, $idCompany ) )   {
//                            $isRepresentative = true; 
//                }
//            }                          
//            if ($isRepresentative) {       
                                 
                /** @var InstitutionInterface $institution */
                $institution = $this->institutionRepo->get($institutionId);  
                $this->institutionRepo->remove($institution);  
                                                     
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic  smí odstraňovat pouze ....");
//            }           
//        }
                
        return $this->redirectSeeLastGet($request);
    }
    //------------------------------------------------------------------------------------------------
    
    
    
     
    /**
     * 
     * @param ServerRequestInterface $request
     * @return type
     */
    public function addContentType (ServerRequestInterface $request) {                 
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
//            
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
//                $isRepresentative = true; 
//            }                        
//            if ($isRepresentative) {
                
                /** @var EventContentTypeInterface $contentType */
                $contentType = $this->container->get(EventContentType::class); //new     
                $contentType->setName((new RequestParams())->getParsedBodyParam($request, 'name') );
                $contentType->setType((new RequestParams())->getParsedBodyParam($request, 'type') );

                $this->eventContentTypeRepo->add($contentType);             
               
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic smí přidávat pouze ...");
//            }
//        }   
        
        return $this->redirectSeeLastGet($request);
    }
    
    
      
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $type
     * @return type
     */
    public function updateContentType (ServerRequestInterface $request, $type) {                    
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
//            
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
//                $isRepresentative = true; 
//            }                        
//            if ($isRepresentative) {
            
                /** @var EventContentTypeInterface $eventContentType */
                $eventContentType = $this->eventContentTypeRepo->get($type);             
                $eventContentType->setName((new RequestParams())->getParsedBodyParam($request, 'name') );
        
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic smí editovat pouze ...");
//            }
//        }           
        
        return $this->redirectSeeLastGet($request);

    }    
      
   
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $institutionId
     * @return type
     */
    public function removeContentType (ServerRequestInterface $request, $type ) {                   
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
//            
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) ) {               
//                if ( $this->representativeRepo->get($loginName, $idCompany ) )   {
//                            $isRepresentative = true; 
//                }
//            }                          
//            if ($isRepresentative) {                                                    
                
                /** @var EventContentTypeInterface $eventContentType */
                $eventContentType = $this->eventContentTypeRepo->get($type);             
                $this->eventContentTypeRepo->remove($eventContentType) ;               
        
                                                     
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic  smí odstraňovat pouze ....");
//            }           
//        }
                
        return $this->redirectSeeLastGet($request);
    }
    
    //------------------
         
    /**
     * 
     * @param ServerRequestInterface $request
     * @return type
     */
    public function addContent (ServerRequestInterface $request) {                 
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
//            
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
//                $isRepresentative = true; 
//            }                        
//            if ($isRepresentative) {
                
                /** @var EventContentInterface $content */
                $content = $this->container->get(EventContent::class); //new     
                $content->setTitle((new RequestParams())->getParsedBodyParam($request, 'title') );
                $content->setPerex((new RequestParams())->getParsedBodyParam($request, 'perex') );
                $content->setParty((new RequestParams())->getParsedBodyParam($request, 'party') );
                
                if ( (new RequestParams())->getParsedBodyParam($request, 'selectInstitution') != self::NULL_VALUE_nahradni )   {
                     $content->setInstitutionIdFk ((new RequestParams())->getParsedBodyParam($request, 'selectInstitution') );
                }                     
                
                if ( (new RequestParams())->getParsedBodyParam($request, 'selectContentType') != self::NULL_VALUE_nahradni )   {
                     $content->setEventContentTypeFk  ((new RequestParams())->getParsedBodyParam($request, 'selectContentType') );
                }     
                
                
                $this->eventContentRepo->add($content);             
               
                
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic smí přidávat pouze ...");
//            }
//        }   
        
        return $this->redirectSeeLastGet($request);
    }
    
    
      
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $type
     * @return type
     */
    public function updateContent (ServerRequestInterface $request, $idContent) {                    
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
//            
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
//                $isRepresentative = true; 
//            }                        
//            if ($isRepresentative) {
            
        
                /** @var EventContentInterface $content */
                $content = $this->eventContentRepo->get($idContent);
                $content->setTitle((new RequestParams())->getParsedBodyParam($request, 'title') );
                $content->setPerex((new RequestParams())->getParsedBodyParam($request, 'perex') );
                $content->setParty((new RequestParams())->getParsedBodyParam($request, 'party') );
                
                /* cvicne */  $selecI =  (new RequestParams())->getParsedBodyParam($request, 'selectInstitution') ;
                
                if ( (new RequestParams())->getParsedBodyParam($request, 'selectInstitution') != self::NULL_VALUE_nahradni )   {
                     $content->setInstitutionIdFk ((new RequestParams())->getParsedBodyParam($request, 'selectInstitution') );
                }     
                else {
                    $content->setInstitutionIdFk( null );
                }
                               
                if ( (new RequestParams())->getParsedBodyParam($request, 'selectContentType') != self::NULL_VALUE_nahradni )   {
                     $content->setEventContentTypeFk  ((new RequestParams())->getParsedBodyParam($request, 'selectContentType') );
                }     
                else {
                    $content->setEventContentTypeFk( null );
                }

                
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic smí editovat pouze ...");
//            }
//        }           
        
        return $this->redirectSeeLastGet($request);

    }    
      
   
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $institutionId
     * @return type
     */
    public function removeContent (ServerRequestInterface $request, $idContent ) {                   
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
//            
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) ) {               
//                if ( $this->representativeRepo->get($loginName, $idCompany ) )   {
//                            $isRepresentative = true; 
//                }
//            }                          
//            if ($isRepresentative) {                                                    
                
                /** @var EventContentInterface $eventContent */
                $content = $this->eventContentRepo->get($idContent);             
                $this->eventContentRepo->remove($content) ;               
        
                                                     
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic  smí odstraňovat pouze ....");
//            }           
//        }
                
        return $this->redirectSeeLastGet($request);
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
