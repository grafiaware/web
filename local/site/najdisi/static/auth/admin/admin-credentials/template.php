<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Core\Text\Text;
use Pes\Core\Text\Html;

use Component\ViewModel\StatusViewModel;
use Component\ViewModel\StatusViewModelInterface;
use Access\Enum\RoleEnum;

use Auth\Model\Repository\CredentialsRepoInterface;
use Auth\Model\Repository\CredentialsRepo;
use Auth\Model\Repository\RoleRepoInterface;
use Auth\Model\Repository\RoleRepo;
use Auth\Model\Repository\LoginRepo;
use Auth\Model\Repository\LoginRepoInterface;

use Auth\Model\Entity\RoleInterface;
use Auth\Model\Entity\CredentialsInterface;
use Auth\Model\Entity\LoginInterface;


use Auth\Middleware\Login\Controler\AuthControler;
use Auth\Model\Dao\RegistrationDao;
use Auth\Model\Dao\LoginProfileDao;
use Auth\Service\AccountService;
use Auth\Service\UnconfirmedRegistrationCleaner;
use Auth\Middleware\Login\Controler\UnconfirmedRegistrationControler;
use Application\Feature\FeatureFlags;



/** @var PhpTemplateRendererInterface $this */
/** @var StatusViewModelInterface $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
$getEditable = $statusViewModel->getRepresentativeActions()->getDataEditable();   //TODO: SV editace dat pro events admin
$userRole = $statusViewModel->getUserRole();
// A6: zakládání účtů – supervisor a events_administrator (docs/vp/zadani/A6.md)
$features = FeatureFlags::fromConfiguration();
$canCreateAccounts = $features->isEnabled('vp.zakladani-uctu') && AccountService::canCreateAccounts($userRole);
$displayNameEnabled = $features->isEnabled('vp.osobni-jmeno');
// A3: nepotvrzené registrace – výpis pro supervisor a events_administrator, úklid jen supervisor (docs/vp/zadani/A3.md)
$showUnconfirmed = $features->isEnabled('vp.uklid-registraci')
        && in_array($userRole, [RoleEnum::SUPERVISOR, RoleEnum::EVENTS_ADMINISTRATOR], true);
if ($userRole == RoleEnum::EVENTS_ADMINISTRATOR || $canCreateAccounts || $showUnconfirmed) {
    /** @var CredentialsRepoInterface $credentialsRepo */ 
    $credentialsRepo = $container->get(CredentialsRepo::class );
    /** @var RoleRepoInterface $roleRepo */ 
    $roleRepo = $container->get(RoleRepo::class );
    /** @var LoginRepoInterface $loginRepo */ 
    $loginRepo = $container->get(LoginRepo::class );
      
     
    $selectLoginNames = [];
    $selectLoginNames [''] =  "vyber - povinné pole" ;
    $allLogin = $loginRepo->find(' 1=1 order by login_name ', [] );
    $loginsArray=[];
    /** @var  LoginInterface $log */
    foreach ($allLogin as $log) {    
        $loginsArray ['loginName'] = $log->getLoginName();               
        $selectLoginNames [ $log->getLoginName() ] =  $log->getLoginName()  ;
    }
  
    
    $selectRoles = [];
    $selectRoles [AuthControler::NULL_VALUE] =  "" ;
    $roles = $roleRepo->findAll();
        /** @var RoleInterface $role */ 
    foreach ( $roles as $role ) {
        $selectRoles [$role->getRole()] = $role->getRole() ;
    }    
    
    $allowedRoles = $canCreateAccounts ? AccountService::allowedRoles($userRole, array_filter(array_keys($selectRoles), fn($r) => $r !== AuthControler::NULL_VALUE)) : [];
    // A6: e-mail (a osobní jméno A8) u účtů ve výpisu
    $emails = [];
    $displayNames = [];
    if ($canCreateAccounts) {
        foreach ($container->get(RegistrationDao::class)->find('1=1', []) as $registrationRow) {
            $emails[$registrationRow['login_name_fk']] = (string) $registrationRow['email'];
        }
        if ($displayNameEnabled) {
            foreach ($container->get(LoginProfileDao::class)->find('display_name IS NOT NULL', []) as $profileRow) {
                $displayNames[$profileRow['login_name_fk']] = (string) $profileRow['display_name'];
            }
        }
    }

    $selecty = [];
    $selecty['selectRoles'] = $selectRoles;
    $selecty['selectLoginNames'] = $selectLoginNames;

    //---------------------------------------------------------       
    // Credentials všechny
    $credentialsEntities = $credentialsRepo->find(' 1=1 order by login_name_fk ', [] );               
    if ($credentialsEntities) {   
            /** @var CredentialsInterface $entity */
            foreach ($credentialsEntities as $entity) {
                      $nu1 = $entity->getRoleFk();
                      $nu2 = $entity->getLoginNameFk();
                
                $credential = $credentialsRepo->get( $entity->getRoleFk() ?? ''    ) ;               
                $credentials[] = [
                    'roleFk' => ($entity->getRoleFk()) ?? AuthControler::NULL_VALUE , 
                    'selectRoles' =>  $selectRoles,                     
                    'loginNameFk' =>  $entity->getLoginNameFk(),
                    'passwordHash' => $entity->getPasswordHash(),
                    'showAccountInfo' => $canCreateAccounts,
                    'email' => $emails[$entity->getLoginNameFk()] ?? '',
                    'displayName' => $displayNames[$entity->getLoginNameFk()] ?? '',
                    'canSendSetPasswordLink' => $canCreateAccounts && in_array($entity->getRoleFk(), $allowedRoles, true)
                            && ($emails[$entity->getLoginNameFk()] ?? '') !== '',
                    ];
            }   
    }
//    else {                
//    }
                                              
  ?>
    
    <?php if ($showUnconfirmed):
        /** @var UnconfirmedRegistrationCleaner $cleaner */
        $cleaner = $container->get(UnconfirmedRegistrationCleaner::class);
        $unconfirmedDays = UnconfirmedRegistrationControler::days(); ?>
        <?= $this->insert(__DIR__.'/unconfirmed.php', [
                'pendingRepresentatives' => $cleaner->pendingRepresentatives($unconfirmedDays),
                'preview' => $cleaner->preview($unconfirmedDays),
                'days' => $unconfirmedDays,
                'canClean' => $userRole === RoleEnum::SUPERVISOR,
            ]) ?>
    <?php endif; ?>
    <?php if ($canCreateAccounts): ?>
        <?= $this->insert(__DIR__.'/new-account.php', [
                'allowedRoles' => $allowedRoles,
                'displayNameEnabled' => $displayNameEnabled,
                'passwordPattern' => ConfigurationCache::auth()['passwordPattern'],
                'passwordInfo' => ConfigurationCache::auth()['passwordInfo'],
            ]) ?>
    <?php endif; ?>
    <div class="ui styled fluid accordion">           
        <div>             
           <b>CREDENTIALS-tabulka</b>                                 
        </div>                          
        ------------------------------------------------------                      
        <div>      
            <?php  if (isset ($credentialsEntities) ) {   ?>                                
                    <span>Login Name | <?= $canCreateAccounts ? 'E-mail | ' : '' ?>PasswordHash | Role</span>                                 
                <?= $this->repeat(__DIR__.'/credentials.php',  $credentials )  ?>                         
            <?php }   ?>      
                                      
        </div>
    </div>    
<?php
} else {
    echo Html::p("Stránka je určena pouze pro editaci v administraci.", ["class"=>"ui orange segment"]);
}
?>
