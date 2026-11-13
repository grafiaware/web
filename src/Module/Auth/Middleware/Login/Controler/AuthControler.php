<?php
namespace Auth\Middleware\Login\Controler;

use Access\Enum\RoleEnum;
use Access\Enum\AccessActionEnum;

use FrontControler\FrontControlerAbstract;
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
use Auth\Service\RoleGrantPolicy;
use Auth\Service\RoleChangeLog;


/**
 * Description of AuthControler
 *
 * @author vlse2610
 */
class AuthControler extends FrontControlerAbstract {
        
    const NULL_VALUE = "Toto je speciální hodnota představující NULL";        
    
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
    
    private ?RoleGrantPolicy $roleGrantPolicy = null;
    private ?RoleChangeLog $roleChangeLog = null;

    /** V testech lze podstrčit; jinak podle přepínače vp.sprava-roli (A9). */
    public function setRoleGrantPolicy(RoleGrantPolicy $policy): static {
        $this->roleGrantPolicy = $policy;
        return $this;
    }

    public function setRoleChangeLog(?RoleChangeLog $log): static {
        $this->roleChangeLog = $log;
        return $this;
    }

    private function policy(): RoleGrantPolicy {
        return $this->roleGrantPolicy ??= RoleGrantPolicy::fromConfiguration();
    }

    /**
     * A9: hrubé oprávnění k akcím kontroléru – role, které spravují účty nebo tabulku role.
     * Jemná kontrola (kterou roli komu) je v RoleGrantPolicy u každé akce.
     */
    protected function getActionPermissions(): array {
        $permissions = [];
        foreach (array_unique(array_merge($this->policy()->managerRoles(),
                $this->policy()->grantorsEnabled() ? [RoleEnum::GLOBAL_ROLE_GRANTOR, RoleEnum::AUTH_ROLE_GRANTOR] : [RoleEnum::SUPERVISOR, RoleEnum::EVENTS_ADMINISTRATOR])) as $role) {
            $permissions[$role] = [AccessActionEnum::GET => true, AccessActionEnum::POST => true];
        }
        return $permissions;
    }

    private function actorLogin(): string {
        return (string) $this->statusSecurityRepo->get()->getLoginAggregate()?->getLoginName();
    }

    private function actorRole(): ?string {
        return $this->statusSecurityRepo->get()->getLoginAggregate()?->getCredentials()?->getRoleFk();
    }

    /** Úpravy tabulky role (stránka admin-role) – jen role, které ji smí spravovat. */
    private function mayManageRoleTable(): bool {
        return $this->isAllowed(AccessActionEnum::POST) && $this->policy()->canManageRoleTable($this->actorRole());
    }

    /** @return string[] */
    private function allRoles(): array {
        $roles = [];
        foreach ($this->roleRepo->findAll() as $role) {
            $roles[] = $role->getRole();
        }
        return $roles;
    }
     
    /**
     * 
     * @param ServerRequestInterface $request
     * @return type
     */
    public function addRole (ServerRequestInterface $request) {                 
        
        if($this->mayManageRoleTable()) {            

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
        
        if($this->mayManageRoleTable()) {            
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
        
        if($this->mayManageRoleTable()) {                    
            
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
        
        if($this->isAllowed(AccessActionEnum::POST)) {            
                /** @var CredentialsInterface $credentials */
                $credentials = $this->credentialsRepo->get($loginNameFk);
                $selected = (new RequestParams())->getParsedBodyParam($request, 'selectRole', self::NULL_VALUE);
                $newRole = ($selected === self::NULL_VALUE || $selected === '' || $selected === null) ? null : (string) $selected;
                if (!isset($credentials)) {
                    $this->addFlashMessage("Účet neexistuje.", FlashSeverityEnum::WARNING);
                    return $this->redirectSeeLastGet($request);
                }
                $oldRole = $credentials->getRoleFk();
                if ($newRole === $oldRole) {
                    return $this->redirectSeeLastGet($request);
                }
                // A9: kontrola na serveru – nikdo nemění vlastní roli, jen role, které smí přidělit (platí vždy)
                $allRoles = $this->allRoles();
                $denial = ($newRole !== null && !in_array($newRole, $allRoles, true))
                        ? "Role {$newRole} neexistuje."
                        : $this->policy()->roleChangeDenial($this->actorLogin(), $this->actorRole(), (string) $loginNameFk, $oldRole, $newRole, $allRoles);
                if ($denial !== null) {
                    $this->addFlashMessage($denial, FlashSeverityEnum::WARNING);
                    return $this->redirectSeeLastGet($request);
                }
                $credentials->setRoleFk($newRole);
                $this->roleChangeLog?->record($this->actorLogin(), (string) $loginNameFk, $oldRole, $newRole);
                $this->addFlashMessage("Role účtu {$loginNameFk}: " . ($newRole ?? 'bez role') . ".", FlashSeverityEnum::SUCCESS);
        
        } else {
            $this->addFlashMessage("Nemáte oprávnění k požadované operaci!", FlashSeverityEnum::WARNING);
        }           
        
        return $this->redirectSeeLastGet($request);

    }    
      
    
    
}



