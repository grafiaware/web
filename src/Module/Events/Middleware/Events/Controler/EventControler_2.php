<?php

namespace Events\Middleware\Events\Controler;

use FrontControler\FrontControlerAbstract;
use Psr\Http\Message\ServerRequestInterface;
use Pes\Application\AppFactory;
use Pes\Http\Request\RequestParams;
use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;

//use Red\Model\Repository\{
//    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo, EnrollRepo
//};

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Middleware\Api\Controller\Exception\UnexpectedLanguageException;

use Events\Model\Entity\InstitutionTypeInterface;
use Events\Model\Entity\InstitutionType;
use Events\Model\Repository\InstitutionTypeRepoInterface;

use Events\Model\Entity\InstitutionInterface;
use Events\Model\Repository\InstitutionRepoInterface;







//use Events\Model\Arraymodel\Event;
//use Model\Entity\Enroll;


/**
 * 
 */
class EventControler_2 extends FrontControlerAbstract {

    private $enrollRepo;

    private $eventListModel;

    
        
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
    
    
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            
            InstitutionTypeRepoInterface  $institutionTypeRepo,
            InstitutionRepoInterface  $institutionRepo
            
            
           // EnrollRepo $enrollRepo,
           // Event $eventListModel
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->institutionTypeRepo = $institutionTypeRepo;
        $this->institutionRepo = $institutionRepo;
        
        //$this->enrollRepo = $enrollRepo;
        //$this->eventListModel = $eventListModel;
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
        
                   
    //            /** @var PozadovaneVzdelaniInterface $pozadovaneVzdelani */
    //            $pozadovaneVzdelani = $this->pozadovaneVzdelaniRepo->get($stupen);            
    //            $pozadovaneVzdelani->setVzdelani((new RequestParams())->getParsedBodyParam($request, 'vzdelani') );              
           
        
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
                    
           
    //            /** @var PozadovaneVzdelaniInterface $pozadovaneVzdelani */
    //            $pozadovaneVzdelani = $this->pozadovaneVzdelaniRepo->get($stupen);            
    //            $pozadovaneVzdelani = $this->pozadovaneVzdelaniRepo->remove($pozadovaneVzdelani);

                                                
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

                
    //                    /** @var InstitutionTypeInterface $institutionType */
    //                    $institutionType = $this->container->get(InstitutionType::class); //new           
    //                    $institutionType->setInstitutionType((new RequestParams())->getParsedBodyParam($request, 'institutionType') );
    //    
    //                    $this->institutionTypeRepo->add($institutionType);             
               
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
    public function updateInstitution (ServerRequestInterface $request, $institutionTypeId) {                    
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
        
                   
    //            /** @var PozadovaneVzdelaniInterface $pozadovaneVzdelani */
    //            $pozadovaneVzdelani = $this->pozadovaneVzdelaniRepo->get($stupen);            
    //            $pozadovaneVzdelani->setVzdelani((new RequestParams())->getParsedBodyParam($request, 'vzdelani') );              
           
        
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
    public function removeInstitution (ServerRequestInterface $request, $institutionTypeId ) {                   
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
                    
           
    //            /** @var PozadovaneVzdelaniInterface $pozadovaneVzdelani */
    //            $pozadovaneVzdelani = $this->pozadovaneVzdelaniRepo->get($stupen);            
    //            $pozadovaneVzdelani = $this->pozadovaneVzdelaniRepo->remove($pozadovaneVzdelani);

                                                
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic  smí odstraňovat pouze ....");
//            }           
//        }
                
        return $this->redirectSeeLastGet($request);
    }
            
    
    
    
    
}
