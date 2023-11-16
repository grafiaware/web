<?php
namespace Auth\Middleware\Login\Controller;


use FrontControler\PresentationFrontControlerAbstract;
use Psr\Http\Message\ServerRequestInterface;
use Pes\Http\Request\RequestParams;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Auth\Model\Repository\RoleRepoInterface;
use Auth\Model\Repository\CredentialsRepoInterface;

use Auth\Model\Entity\CredentialsInterface;
use Auth\Model\Entity\RoleInterface;
use Auth\Model\Entity\Role;




/**
 * Description of AuthController
 *
 * @author vlse2610
 */
class AuthController extends PresentationFrontControlerAbstract {
        
    const NULL_VALUE_nahradni = "Toto je speciální hodnota představující NULL";        
    
    /**
     * 
     * @var CredentialsRepoInterface
     */
    private $credentialsRepo;
    /**
     * 
     * @var RoleRepoInterface
     */
    private $roleRepo;    
    
    
    

     public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            
            RoleRepoInterface  $roleRepo,
            CredentialsRepoInterface  $credentialsRepo
             
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
       
        $this->roleRepo = $roleRepo;
        $this->credentialsRepo = $credentialsRepo;               
             
    }
    
     
    /**
     * 
     * @param ServerRequestInterface $request
     * @return type
     */
    public function addRole (ServerRequestInterface $request) {                 
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
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
//                $isRepresentative = true; 
//            }                        
//            if ($isRepresentative) {
                
                /** @var RoleInterface $roleE*/
                $roleE = new Role(); //new
                $roleE->setRole((new RequestParams())->getParsedBodyParam($request, 'role') );                
     
                $this->roleRepo->add($roleE);             
               
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
    public function updateRole (ServerRequestInterface $request, $role) {                    
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
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
//                $isRepresentative = true; 
//            }                        
//            if ($isRepresentative) {
            
                /** @var Role $roleE */
                $roleE = $this->roleRepo->get($role);             
                $roleE->setRole((new RequestParams())->getParsedBodyParam($request, 'role') );                

        
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
    public function removeRole (ServerRequestInterface $request, $role ) {                   
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
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) ) {               
//                if ( $this->representativeRepo->get($loginName, $idCompany ) )   {
//                            $isRepresentative = true; 
//                }
//            }                          
//            if ($isRepresentative) {                                                    
                
                /** @var Role $roleE */
                $roleE = $this->roleRepo->get($role);   
                $this->roleRepo->remove($roleE) ;                                                            
                                                     
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic  smí odstraňovat pouze ....");
//            }           
//        }
                
        return $this->redirectSeeLastGet($request);
    }
    
    
    
    
         
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $loginNameFk
     * @return type
     */
    public function updateCredentials (ServerRequestInterface $request, $loginNameFk) {                    
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
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
//                $isRepresentative = true; 
//            }                        
//            if ($isRepresentative) {
            
                /** @var CredentialsInterface $credentials */
                $credentials = $this->credentialsRepo->get($loginNameFk);             
                //$credentials->setRoleFk((new RequestParams())->getParsedBodyParam($request, 'selectRole') );    
                
                
                 if ( (new RequestParams())->getParsedBodyParam($request, 'selectRole') != self::NULL_VALUE_nahradni )   {
                      $credentials->setRoleFk ((new RequestParams())->getParsedBodyParam($request, 'selectRole') );
                }    
                 else {
                    $credentials->setRoleFk( null );
                }     
                

        
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic smí editovat pouze ...");
//            }
//        }           
        
        return $this->redirectSeeLastGet($request);

    }    
      
    
    
}



