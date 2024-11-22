<?php

namespace Events\Middleware\Events\Controler;

use FrontControler\FrontControlerAbstract;
use Psr\Http\Message\ServerRequestInterface;
use Pes\Http\Request\RequestParams;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

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

use Events\Model\Entity\EventLinkPhaseInterface;
use Events\Model\Entity\EventLinkPhase;
use Events\Model\Repository\EventLinkPhaseRepoInterface;

use Events\Model\Entity\EventLinkInterface;
use Events\Model\Entity\EventLink;
use Events\Model\Repository\EventLinkRepoInterface;



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
    /**
     *  @var EventLinkPhaseRepoInterface
     */
    private $eventLinkPhaseRepo;
    /**
     *  @var EventLinkRepoInterface
     */
    private $eventLinkRepo;
    
    
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            
            InstitutionRepoInterface  $institutionRepo,
            InstitutionTypeRepoInterface  $institutionTypeRepo,
            EventContentRepoInterface  $eventContentRepo,
            EventContentTypeRepoInterface  $eventContentTypeRepo,
            EventLinkPhaseRepoInterface $eventLinkPhaseRepo,
            EventLinkRepoInterface $eventLinkRepo
                       
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
       
        $this->institutionRepo = $institutionRepo;
        $this->institutionTypeRepo = $institutionTypeRepo;        
        $this->eventContentRepo = $eventContentRepo;
        $this->eventContentTypeRepo = $eventContentTypeRepo;
        
        $this->eventLinkPhaseRepo = $eventLinkPhaseRepo;
        $this->eventLinkRepo = $eventLinkRepo;
             
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
//            $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? ''; 
//            
//            if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
//                $isRepresentative = true; 
//            }                        
//            if ($isRepresentative) {
               
                    /** @var InstitutionTypeInterface $institutionType */
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
//            $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? ''; 
//            
//            if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative']) 
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
//            $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? '';           
//            
//            if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative']) ) {               
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
//            $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? ''; 
//            
//            if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
//                $isRepresentative = true; 
//            }                        
//            if ($isRepresentative) {
                
                /** @var InstitutionInterface $institution */
                $institution = new Institution(); //new      
                $institution->setName((new RequestParams())->getParsedBodyParam($request, 'institutionName') );
                
                //$institution->setInstitutionTypeId((new RequestParams())->getParsedBodyParam($request, 'selinstitutionTypeId') );
                if ( (new RequestParams())->getParsedBodyParam($request, 'selectInstitutionTypeId') != self::NULL_VALUE_nahradni )   {
                      $institution->setInstitutionTypeId ((new RequestParams())->getParsedBodyParam($request, 'selectInstitutionTypeId') );
                }    
                 else {
                    $institution->setInstitutionTypeId( null );
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
//            $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? ''; 
//            
//            if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
//                $isRepresentative = true; 
//            }                        
//            if ($isRepresentative) {
            
                /** @var InstitutionInterface $institution */
                $institution = $this->institutionRepo->get($institutionId);             
                $institution->setName((new RequestParams())->getParsedBodyParam($request, 'institutionName') );
                //$institution->setInstitutionTypeId((new RequestParams())->getParsedBodyParam($request, 'selinstitutionTypeId') );     
                
                if ( (new RequestParams())->getParsedBodyParam($request, 'selectInstitutionTypeId') != self::NULL_VALUE_nahradni )   {
                    $institution->setInstitutionTypeId ((new RequestParams())->getParsedBodyParam($request, 'selectInstitutionTypeId') );
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
//            $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? '';           
//            
//            if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative']) ) {               
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
//            $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? ''; 
//            
//            if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
//                $isRepresentative = true; 
//            }                        
//            if ($isRepresentative) {
                
                /** @var EventContentTypeInterface $contentType */
                $contentType = new EventContentType(); //new
                $contentType->setType((new RequestParams())->getParsedBodyParam($request, 'type') );                
                $contentType->setName((new RequestParams())->getParsedBodyParam($request, 'name') );
     
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
    public function updateContentType (ServerRequestInterface $request, $id) {                    
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
//            if ($isRepresentative) {
            
                /** @var EventContentTypeInterface $eventContentType */
                $eventContentType = $this->eventContentTypeRepo->get($id);             
                $eventContentType->setType((new RequestParams())->getParsedBodyParam($request, 'type') );                
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
     * @param type $type
     * @return type
     */
    public function removeContentType (ServerRequestInterface $request, $id ) {                   
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
//                if ( $this->representativeRepo->get($loginName, $idCompany ) )   {
//                            $isRepresentative = true; 
//                }
//            }                          
//            if ($isRepresentative) {                                                    
                
                /** @var EventContentTypeInterface $eventContentType */
                $eventContentType = $this->eventContentTypeRepo->get($id);             
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
//            $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? ''; 
//            
//            if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
//                $isRepresentative = true; 
//            }                        
//            if ($isRepresentative) {
                
                /** @var EventContentInterface $content */
                $content = new EventContent();  //new
                $a = (new RequestParams())->getParsedBodyParam($request, 'title') ;                                                            
                $content->setTitle((new RequestParams())->getParsedBodyParam($request, 'title') );               
                $content->setPerex((new RequestParams())->getParsedBodyParam($request, 'perex') );
                $content->setParty((new RequestParams())->getParsedBodyParam($request, 'party') );
                
                if ( (new RequestParams())->getParsedBodyParam($request, 'selectInstitutionId') != self::NULL_VALUE_nahradni )   {
                     $content->setInstitutionIdFk ((new RequestParams())->getParsedBodyParam($request, 'selectInstitutionId') );
                }   
                else {
                    $content->setInstitutionIdFk( null );
                }     
                
                //not null
                $content->setEventContentTypeIdFk  ((new RequestParams())->getParsedBodyParam($request, 'selectContentTypeId') );
            
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
     * @param type $idContent
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
//            $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? ''; 
//            
//            if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
//                $isRepresentative = true; 
//            }                        
//            if ($isRepresentative) {
            
        
                /** @var EventContentInterface $content */
                $content = $this->eventContentRepo->get($idContent);
                $content->setTitle((new RequestParams())->getParsedBodyParam($request, 'title') );
                $content->setPerex((new RequestParams())->getParsedBodyParam($request, 'perex') );
                $content->setParty((new RequestParams())->getParsedBodyParam($request, 'party') );
                
                /* cvicne */  $selecI =  (new RequestParams())->getParsedBodyParam($request, 'selectInstitutionId') ;
                
                if ( (new RequestParams())->getParsedBodyParam($request, 'selectInstitutionId') != self::NULL_VALUE_nahradni )   {
                     $content->setInstitutionIdFk ((new RequestParams())->getParsedBodyParam($request, 'selectInstitutionId') );
                }     
                else {
                    $content->setInstitutionIdFk( null );
                }
                  
                //not null
               $content->setEventContentTypeIdFk  ((new RequestParams())->getParsedBodyParam($request, 'selectContentTypeId') );
                

                
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic smí editovat pouze ...");
//            }
//        }           
        
        return $this->redirectSeeLastGet($request);

    }    
      
  
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $idContent
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
//            $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? '';           
//            
//            if(isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative']) ) {               
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
    
    //------------------------------------------------------------------
    
    
    
     /**
     * 
     * @param ServerRequestInterface $request
     * @return type
     */
    public function addEventLinkPhase (ServerRequestInterface $request) {                 
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
//            if ($isRepresentative) {

                
                    /** @var EventLinkPhaseInterface $eventLinkPhase */
                    $eventLinkPhase = new EventLinkPhase(); //new           
                    $eventLinkPhase->setText((new RequestParams())->getParsedBodyParam($request, 'eventLinkPhaseText') );
    
                    $this->eventLinkPhaseRepo->add($eventLinkPhase);             
               
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic smí přidávat pouze ...");
//            }
//        }   
        
        return $this->redirectSeeLastGet($request);
    }
    
          
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $eventLinkPhaseId
     * @return type
     */
    public function updateEventLinkPhase (ServerRequestInterface $request, $eventLinkPhaseId) {                    
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
//            if ($isRepresentative) {
                           
                /** @var EventLinkPhase $eventLinkPhase */               
                $eventLinkPhase =  $this->eventLinkPhaseRepo->get( $eventLinkPhaseId );         
                $eventLinkPhase->setText((new RequestParams())->getParsedBodyParam($request, 'eventLinkPhaseText') );
    
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic smí editovat pouze ...");
//            }
//        }           
        
        return $this->redirectSeeLastGet($request);

    }    
      

    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $eventLinkPhaseId
     * @return type
     */
    public function removeEventLinkPhase (ServerRequestInterface $request, $eventLinkPhaseId ) {                   
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
//                if ( $this->representativeRepo->get($loginName, $idCompany ) )   {
//                            $isRepresentative = true; 
//                }
//            }                          
//            if ($isRepresentative) {                                                    
                
                /** @var EventLinkPhase $eventLinkPhase */               
                $eventLinkPhase =  $this->eventLinkPhaseRepo->get( $eventLinkPhaseId );   
                $this->eventLinkPhaseRepo->remove($eventLinkPhase);                
                                                
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic  smí odstraňovat pouze ....");
//            }           
//        }
                
        return $this->redirectSeeLastGet($request);
    }
            
//-----------------------------------------------------------------------------------------
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @return type
     */
    public function addEventLink (ServerRequestInterface $request) {                 
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
//            if ($isRepresentative) {
                
                /** @var EventLinkInterface $eventLink */
                $eventLink = new EventLink(); //new
                $eventLink->setShow((new RequestParams())->getParsedBodyParam($request, 'show') ?? 0 );
                $eventLink->setHref((new RequestParams())->getParsedBodyParam($request, 'href') );                     
                //not null            
                $eventLink->setLinkPhaseIdFk ((new RequestParams())->getParsedBodyParam($request, 'eventLinkPhaseId') );                                       

                $this->eventLinkRepo->add($eventLink);             
               
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic smí přidávat pouze ...");
//            }
//        }   
        
        return $this->redirectSeeLastGet($request);
    }
    
    
      
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $eventLinkId
     * @return type
     */
    public function updateEventLink (ServerRequestInterface $request, $eventLinkId) {                    
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
//            if ($isRepresentative) {
            
                /** @var EventLinkInterface $eventLink */
                $eventLink = $this->eventLinkRepo->get($eventLinkId);             
                $eventLink->setShow((new RequestParams())->getParsedBodyParam($request, 'show') ?? 0 );
                $eventLink->setHref((new RequestParams())->getParsedBodyParam($request, 'href') );
               
                 //not null            
                $eventLink->setLinkPhaseIdFk ((new RequestParams())->getParsedBodyParam($request, 'eventLinkPhaseId') );                        
        
        return $this->redirectSeeLastGet($request);

    }    
      
   
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $eventLinkId
     * @return type
     */
    public function removeEventLink (ServerRequestInterface $request, $eventLinkId ) {                   
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
//                if ( $this->representativeRepo->get($loginName, $idCompany ) )   {
//                            $isRepresentative = true; 
//                }
//            }                          
//            if ($isRepresentative) {       
                                 
                /** @var EventLinkInterface $eventLink */
                $eventLink = $this->eventLinkRepo->get($eventLinkId);             

                $this->eventLinkRepo->remove($eventLink);  
                                                     
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic  smí odstraňovat pouze ....");
//            }           
//        }
                
        return $this->redirectSeeLastGet($request);
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
}
