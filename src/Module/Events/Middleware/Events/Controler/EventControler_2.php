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
use Events\Model\Entity\Institution;
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
            
            InstitutionRepoInterface  $institutionRepo,
            InstitutionTypeRepoInterface  $institutionTypeRepo            
            
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
                $institution = new Institution(); //new           
                $institution->setName((new RequestParams())->getParsedBodyParam($request, 'institutionName') );
                $institution->setInstitutionTypeId((new RequestParams())->getParsedBodyParam($request, 'institutionTypeId') );

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
                $institution->setInstitutionTypeId((new RequestParams())->getParsedBodyParam($request, 'institutionTypeId') );           
        
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
            
    
    
    
    
}
