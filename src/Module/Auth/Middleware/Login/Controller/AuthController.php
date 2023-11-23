<?php
namespace Auth\Middleware\Login\Controller;

use Access\Enum\RoleEnum;
use Access\Enum\AllowedActionEnum;

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

use Status\Model\Enum\FlashSeverityEnum;


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
    
    protected function getActionPermissions(): array {
        return [
            RoleEnum::SUPERVISOR => [AllowedActionEnum::GET => self::class, AllowedActionEnum::POST => self::class],
//            RoleEnum::EDITOR => [AllowedActionEnum::GET => self::class, AllowedActionEnum::POST => self::class],
//            RoleEnum::AUTHENTICATED => [AllowedActionEnum::GET => self::class],
//            RoleEnum::ANONYMOUS => [AllowedActionEnum::GET => self::class]
        ];
    }    
     
    /**
     * 
     * @param ServerRequestInterface $request
     * @return type
     */
    public function addRole (ServerRequestInterface $request) {                 
        
        if($this->isAllowed(AllowedActionEnum::POST)) {            

                /** @var RoleInterface $roleE*/
                $roleE = new Role(); //new                
//                if ( ( (new RequestParams())->getParsedBodyParam($request, 'role') == '') //or  //je required, taky sem nikdy nedojde
//                     //( str_contains( (new RequestParams())->getParsedBodyParam($request, 'role'), ' ' ) ) -- obsahuje-li mezery, nedojde az sem
//                   )
//                {
//                   $this->addFlashMessage("Nepřípustná hodnota!",  FlashSeverityEnum::WARNING);
//                }
//                else {
                    $roleE->setRole((new RequestParams())->getParsedBodyParam($request, 'role') );                     
                    $this->roleRepo->add($roleE);  
//                }
               
        } else {
                $this->addFlashMessage("Nemáte oprávnění k požadované operaci!",  FlashSeverityEnum::WARNING);
        }           
        
        return $this->redirectSeeLastGet($request);
    }
    
    
      
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $role
     * @return type
     */
    public function updateRole (ServerRequestInterface $request, $role) {                    
        
        if($this->isAllowed(AllowedActionEnum::POST)) {            
                /** @var Role $roleE */
                $roleE = $this->roleRepo->get($role);             
                $roleE->setRole((new RequestParams())->getParsedBodyParam($request, 'role') );                        
        } else {
                $this->addFlashMessage("Nemáte oprávnění k požadované operaci!",  FlashSeverityEnum::WARNING);
        }
        
        return $this->redirectSeeLastGet($request);

    }    
      
   
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $role
     * @return type
     */
    public function removeRole (ServerRequestInterface $request, $role ) {                                    
        
        if($this->isAllowed(AllowedActionEnum::POST)) {                    
            
            //KDYZ je pouzite tak NE
            //-------------------------------------------------
            $credArray = $this->credentialsRepo->find( " role_fk = :role ", ['role' => $role ] );
            if (count($credArray)== 0) {            
                /** @var Role $roleE */
                $roleE = $this->roleRepo->get($role);   
                $this->roleRepo->remove($roleE) ;                                                            
            }
            else {
               $this->addFlashMessage("Operaci nelze provést!",  FlashSeverityEnum::WARNING);
            }
                         
            
        } else {
               $this->addFlashMessage("Nemáte oprávnění k požadované operaci!",  FlashSeverityEnum::WARNING);
        }           
                
        return $this->redirectSeeLastGet($request);
    }
    
    
    
    
         
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $loginNameFk
     * @return type
     */
    public function updateCredentials (ServerRequestInterface $request, $loginNameFk) {                    
        
        if($this->isAllowed(AllowedActionEnum::POST)) {            
                /** @var CredentialsInterface $credentials */
                $credentials = $this->credentialsRepo->get($loginNameFk);             
                if ( (new RequestParams())->getParsedBodyParam($request, 'selectRole') != self::NULL_VALUE_nahradni )   {
                      $credentials->setRoleFk ((new RequestParams())->getParsedBodyParam($request, 'selectRole') );
                }    
                 else {
                    $credentials->setRoleFk( null );
                }                     
        
        } else {
            $this->addFlashMessage("Nemáte oprávnění k požadované operaci!", FlashSeverityEnum::WARNING);
        }           
        
        return $this->redirectSeeLastGet($request);

    }    
      
    
    
}



