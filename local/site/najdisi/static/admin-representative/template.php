<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Auth\Model\Entity\LoginAggregateFullInterface;
use Status\Model\Repository\StatusSecurityRepo;

use Pes\Text\Text;
use Pes\Text\Html;

use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyAddressRepo;
use Events\Model\Repository\RepresentativeRepo;
use Events\Model\Repository\LoginRepo;

use Events\Model\Entity\CompanyInterface;
use Events\Model\Entity\CompanyAddressInterface;
use Events\Model\Entity\RepresentativeInterface;
use Events\Model\Entity\LoginInterface;

use Component\ViewModel\StatusViewModel;
use Component\ViewModel\StatusViewModelInterface;
use Access\Enum\RoleEnum;

/** @var PhpTemplateRendererInterface $this */

/** @var StatusViewModelInterface $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
$userRole = $statusViewModel->getUserRole();
if ( $userRole == RoleEnum::EVENTS_ADMINISTRATOR ) {

                // asi navic 
                $statusSecurityRepo = $container->get(StatusSecurityRepo::class);
                /** @var StatusSecurityRepo $statusSecurityRepo */
                $statusSecurity = $statusSecurityRepo->get();
                /** @var LoginAggregateFullInterface $loginAggregate */
                $loginAggregate = $statusSecurity->getLoginAggregate();

                if (isset($loginAggregate)) {
                    $loginName = $loginAggregate->getLoginName();
                    $cred = $loginAggregate->getCredentials();

                    $role = $loginAggregate->getCredentials()->getRoleFk() ?? '';
                }
//    ------------------------------------------------??
    
   
    
    
    /** @var CompanyRepo $companyRepo */ 
    $companyRepo = $container->get(CompanyRepo::class );
    /** @var RepresentativeRepo $representativeRepo */ 
    $representativeRepo = $container->get(RepresentativeRepo::class );
    /** @var LoginRepo $loginRepo */ 
    $loginRepo = $container->get(LoginRepo::class );
    
    $representativeEntities = $representativeRepo->findAll();
    $representatives=[];
    
            foreach ($representativeEntities as $rprs) {
                /** @var RepresentativeInterface $rprs */
                $reprCompany = $companyRepo->get($rprs->getCompanyId());
                
                $representatives[] = [
                    'companyId' => $rprs->getCompanyId(),
                    'companyName' => $reprCompany->getName(),
                    'loginLoginName' => $rprs->getLoginLoginName(),
                    ];
            }
    //------------------------------------------------------------------         
    $selectCompanies =[];    
    $selectCompanies [''] =  "vyber - povinné pole" ;
    $companyEntities = $companyRepo->findAll();
        /** @var CompanyInterface $comp */ 
    foreach ( $companyEntities as $comp) {
        $selectCompanies [$comp->getId()] =  $comp->getName() ;
    }
    
    $selectLogins =[]; 
    $selectLogins [''] =  "vyber - povinné pole" ;
    $loginEntities = $loginRepo->findAll();
        /** @var LoginInterface  $logi */ 
    foreach ( $loginEntities as $logi) {
        $selectLogins [ $logi->getLoginName() ] =  $logi->getLoginName() ;
    }
     
    $selecty['selectCompanies'] = $selectCompanies;
    $selecty['selectLogins']   = $selectLogins;   
        
  ?>
 
 
        <div >
            Representanti firem             
            <div class="ui styled fluid accordion">      
                <?= $this->repeat(__DIR__.'/content/representative.php', $representatives  )  ?>
            </div>
            <p></p>

            Přidej dalšího representanta
            <div class="ui styled fluid accordion">            
                    <?= $this->insert( __DIR__.'/content/representative.php',$selecty ) ?>                     
            </div>            
        
        </div>
        
<?php
} else {
    echo "stránka je určena pouze pro administraci.";
}
?>

