<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;


use Auth\Model\Repository\CredentialsRepoInterface;
use Auth\Model\Repository\CredentialsRepo;
use Auth\Model\Repository\RoleRepoInterface;
use Auth\Model\Repository\RoleRepo;
use Auth\Model\Repository\LoginRepoInterface;

use Auth\Model\Entity\RoleInterface;
use Auth\Model\Entity\CredentialsInterface;

use Auth\Middleware\Login\Controler\AuthControler;
use Component\ViewModel\StatusViewModel;
use Component\ViewModel\StatusViewModelInterface;
use Access\Enum\RoleEnum;

/** @var PhpTemplateRendererInterface $this */

//------------------------------------------------------------------
/** @var StatusViewModelInterface $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
$userRole = $statusViewModel->getUserRole();
if ( $userRole == RoleEnum::EVENTS_ADMINISTRATOR ) {
        
   
    
    
//------------------------------------------------------------------
    /** @var CredentialsRepoInterface $credentialsRepo */ 
    $credentialsRepo = $container->get(CredentialsRepo::class );
    /** @var RoleRepoInterface $roleRepo */ 
    $roleRepo = $container->get(RoleRepo::class );
    
    $roles = $roleRepo->findAll();
    $selectRoles = [];
    $selectRoles [AuthControler::NULL_VALUE] =  "" ;
    /** @var RoleInterface $role */ 
    foreach ( $roles as $role ) {
        $selectRoles [$role->getRole()] = $role->getRole() ;
    }    

    //---------------------------------------------------------       
    // Credentials všechny
    $credentials = $credentialsRepo->find(' 1=1 order by login_name_fk ', [] );               
    /** @var CredentialsInterface $credential */
    foreach ($credentials as $credential) {
            $formArray[] = [
                'loginNameFk' =>  $credential->getLoginNameFk(),
                'passwordHash' => $credential->getPasswordHash(),
                'selectRoles' =>  $selectRoles,
                "selected" => ["selectRole"=>$credential->getRoleFk() ?? AuthControler::NULL_VALUE],
            ];
    }                                        
?>

    <div class="ui styled fluid accordion">           
        <h3>             
           CREDENTIALS-tabulka
        </h3>                          
        <div>      
             <script>

             </script>
                    <?= $this->repeat(__DIR__.'/form.php',  $formArray )  ?>
                <table>
                    <thead>
                        <tr>
                        <th>Login Name</th>
                        <th>Password Hash</th>
                        <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?= $this->repeat(__DIR__.'/row.php',  $formArray )  ?>
                    </tbody>
                </table>
                                      
        </div>
    </div>    
<?php
} else {
    echo Html::p("Stránka je určena pouze pro administraci.", ["class"=>"ui orange segment"]);
}
?>