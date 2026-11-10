<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Core\Text\Text;
use Pes\Core\Text\Html;

use Component\ViewModel\StatusViewModel;
use Component\ViewModel\StatusViewModelInterface;
use Access\Enum\RoleEnum;

use Auth\Model\Repository\RoleRepoInterface;
use Auth\Model\Entity\RoleInterface;

/** @var PhpTemplateRendererInterface $this */
/** @var StatusViewModelInterface $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
$representativeActions = $statusViewModel->getRepresentativeActions();
$getEditable = isset($representativeActions) ? $representativeActions->getDataEditable() : false;   //TODO: SV editace dat pro events admin
$userRole = $statusViewModel->getUserRole();
// A9 (docs/vp/zadani/A9.md): s přepínačem vp.sprava-roli tabulku rolí spravuje global_role_grantor a auth_role_grantor
$rolePolicy = \Auth\Service\RoleGrantPolicy::fromConfiguration();
if ($rolePolicy->grantorsEnabled() ? $rolePolicy->canManageRoleTable($userRole) : $userRole == RoleEnum::EVENTS_ADMINISTRATOR)  {
    
    /** @var RoleRepoInterface $roleRepo */ 
    $roleRepo = $container->get(RoleRepoInterface::class );
    //------------------------------------------------------------------
 
    $allRoles = $roleRepo->findAll();
    $allRolesArray=[];
    /** @var  RoleInterface $role */
    foreach ($allRoles as $role) {               
        $allRolesArray[] =  ['role' => $role->getRole() ];        
    }
             
  ?>

  
    <div class="ui styled fluid accordion">           
           <b>Role uživatelů </b>
        
            <?= $this->repeat(__DIR__.'/role.php',  $allRolesArray)  ?>
            ------ Přidej další roli --------            
                <?= $this->insert( __DIR__.'/role.php' ) ?>                                                                                 
                                      
    </div>
<?php
} else {
    echo Html::p("Stránka je určena pouze pro editaci v administraci.", ["class"=>"ui orange segment"]);
}
?>
