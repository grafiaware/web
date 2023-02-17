<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Middleware\Events\Controler;

use FrontControler\FrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Application\AppFactory;
use Pes\Http\Request\RequestParams;
use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;

use Red\Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo, EnrollRepo
};

use Middleware\Api\Controller\Exception\UnexpectedLanguageException;

use \Events\Model\Arraymodel\Event;
use \Model\Entity\Enroll;

/**
 * Description of PostController
 *
 * @author pes2704
 */
class EventControler extends FrontControlerAbstract {

    private $enrollRepo;

    private $eventListModel;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            EnrollRepo $enrollRepo,
            Event $eventListModel
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->enrollRepo = $enrollRepo;
        $this->eventListModel = $eventListModel;
    }

    public function XX(){
        $loginAggregateCredentials = $this->statusSecurityRepo->get()->getLoginAggregate();
        if (isset($loginAggregateCredentials)) {
            $role = $loginAggregateCredentials->getCredentials()->getRole();
            $permission = [
                'sup' => true,
                'editor' => true
            ];
            if (array_key_exists($role, $permission) AND $permission[$role]) {

            }
        }
    }


    public function enroll(ServerRequestInterface $request) {
        $requestedEventId = (new RequestParams())->getParsedBodyParam($request, 'event_enroll');
        if (isset($requestedEventId)) {
            $boxItem = $this->eventListModel->getEventBoxItem($requestedEventId);
            if (isset($boxItem)) {
                $loginAggregateCredentials = $this->statusSecurityRepo->get()->getLoginAggregate();
                if (isset($loginAggregateCredentials)) {
                    $loginName = $loginAggregateCredentials->getLoginName();
                    $enroll = new Enroll();
                    $enroll->setLoginName($loginName)->setEventid($requestedEventId);
                    $this->enrollRepo->add($enroll);
                    $title = $boxItem['title'];
                    $this->addFlashMessage("Přihlášeno! ".PHP_EOL.$title.PHP_EOL."Ve svém návštěvnickém profilu v menu najdete odkaz.");
                }
            }
        }

        return $this->redirectSeeLastGet($request); // 303 See Other
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

                
                /** @var PozadovaneVzdelaniInterface $pozadovaneVzdelani */
                $pozadovaneVzdelani = $this->container->get(PozadovaneVzdelani::class); //new       

                $pozadovaneVzdelani->setStupen((new RequestParams())->getParsedBodyParam($request, 'stupen') );
                $pozadovaneVzdelani->setVzdelani((new RequestParams())->getParsedBodyParam($request, 'vzdelani') );              

                $this->pozadovaneVzdelaniRepo->add($pozadovaneVzdelani);             
               
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic smí přidávat pouze ...");
//            }
//        }   
        
        return $this->redirectSeeLastGet($request);
    }
    
    
  
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $stupen
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
        
                   
            /** @var PozadovaneVzdelaniInterface $pozadovaneVzdelani */
            $pozadovaneVzdelani = $this->pozadovaneVzdelaniRepo->get($stupen);            
            $pozadovaneVzdelani->setVzdelani((new RequestParams())->getParsedBodyParam($request, 'vzdelani') );              
           
        
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic smí editovat pouze ...");
//            }
//        }           
        
        return $this->redirectSeeLastGet($request);

    }    
      

    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $stupen
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
                    
           
            /** @var PozadovaneVzdelaniInterface $pozadovaneVzdelani */
            $pozadovaneVzdelani = $this->pozadovaneVzdelaniRepo->get($stupen);            
            $pozadovaneVzdelani = $this->pozadovaneVzdelaniRepo->remove($pozadovaneVzdelani);

                                                
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic  smí odstraňovat pouze ....");
//            }           
//        }
                
        return $this->redirectSeeLastGet($request);
    }
            
    
    
    
    
    
}
