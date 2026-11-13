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
use Auth\Service\AccountOrigin;
use Auth\Service\RoleGrantPolicy;
use Application\Feature\FeatureFlags;



/** @var PhpTemplateRendererInterface $this */
/** @var StatusViewModelInterface $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
$getEditable = $statusViewModel->getRepresentativeActions()->getDataEditable();   //TODO: SV editace dat pro events admin
$userRole = $statusViewModel->getUserRole();
// A6: zakládání účtů (docs/vp/zadani/A6.md); A9: správa účtů a rolí – role *_role_grantor (docs/vp/zadani/A9.md)
$features = FeatureFlags::fromConfiguration();
$policy = RoleGrantPolicy::fromConfiguration($features);
$a9 = $policy->grantorsEnabled();
$canCreateAccounts = $features->isEnabled('vp.zakladani-uctu') && $policy->canManageAccounts($userRole);
$displayNameEnabled = $features->isEnabled('vp.osobni-jmeno');
// A3: nepotvrzené registrace – výpis a úklid podle RoleGrantPolicy (docs/vp/zadani/A3.md)
$showUnconfirmed = $features->isEnabled('vp.uklid-registraci') && $policy->canViewUnconfirmed($userRole);
$pageAllowed = ($a9 ? $policy->canManageAccounts($userRole) : ($userRole == RoleEnum::EVENTS_ADMINISTRATOR || $canCreateAccounts))
        || $showUnconfirmed;
if ($pageAllowed) {
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
    
    $allRoleNames = array_values(array_filter(array_keys($selectRoles), fn($r) => $r !== AuthControler::NULL_VALUE));
    $allowedRoles = ($canCreateAccounts || $a9) ? $policy->grantableRoles($userRole, $allRoleNames) : [];
    $userLogin = (string) $statusViewModel->getUserLoginName();
    // A6: e-mail (a osobní jméno A8) u účtů ve výpisu
    $emails = [];
    $displayNames = [];
    $profiles = $a9 ? $container->get(LoginProfileDao::class)->findProfiles() : [];
    if ($canCreateAccounts || $a9) {
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
                    'canSendSetPasswordLink' => $canCreateAccounts && (in_array($entity->getRoleFk(), $allowedRoles, true)
                                || ($a9 && $entity->getRoleFk() === null))
                            && ($emails[$entity->getLoginNameFk()] ?? '') !== '',
                    ];
                if ($a9) {
                    // A9: typ účtu, poslední přihlášení, co smí přihlášený upravit
                    $login = $entity->getLoginNameFk();
                    $origin = AccountOrigin::resolve($profiles[$login]['origin'] ?? null, $emails[$login] ?? null);
                    $roleEditable = $policy->canEditRoleOf($userLogin, $userRole, $login, $entity->getRoleFk(), $allRoleNames);
                    $credentials[array_key_last($credentials)] += [
                        'origin' => $origin,
                        'originLabel' => AccountOrigin::label($origin),
                        'lastLogin' => $profiles[$login]['lastLogin'] ?? null,
                        'hasPassword' => (string) $entity->getPasswordHash() !== '',
                        'roleEditable' => $roleEditable,
                        'grantableRoles' => $allowedRoles,
                        'emailEditable' => $roleEditable && $origin !== AccountOrigin::SELF && $policy->canEditEmail($userRole),
                        'displayNameEditable' => $roleEditable && $displayNameEnabled,
                        'displayName' => $profiles[$login]['displayName'] ?? '',
                    ];
                }
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
                'canClean' => $policy->canCleanUnconfirmed($userRole),
            ]) ?>
    <?php endif; ?>
    <?php if ($a9 && $canCreateAccounts): ?>
        <?= $this->insert(__DIR__.'/new-account-a9.php', [
                'allowedRoles' => $allowedRoles,
                'displayNameEnabled' => $displayNameEnabled,
                'ttlHours' => (int) (ConfigurationCache::auth()['setPasswordLinkTtlHours'] ?? 72),
            ]) ?>
    <?php elseif ($canCreateAccounts): ?>
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
            <?php  if (isset ($credentialsEntities) && $a9) {   ?>
                <?= $this->insert(__DIR__.'/credentials-a9.php', ['rows' => $credentials, 'linkTtlHours' => (int) (ConfigurationCache::auth()['setPasswordLinkTtlHours'] ?? 72)]) ?>
            <?php } elseif (isset ($credentialsEntities) ) {   ?>                                
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
