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

/** @var PhpTemplateRendererInterface $this */


   $statusSecurityRepo = $container->get(StatusSecurityRepo::class);
    /** @var StatusSecurityRepo $statusSecurityRepo */
    $statusSecurity = $statusSecurityRepo->get();
    /** @var LoginAggregateFullInterface $loginAggregate */
    $loginAggregate = $statusSecurity->getLoginAggregate();

    if (isset($loginAggregate)) {
        $loginName = $loginAggregate->getLoginName();
        $cred = $loginAggregate->getCredentials();
        
        $role = $loginAggregate->getCredentials()->getRole() ?? '';
    }
//    ------------------------------------------------
    
   
    
    
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
                //$selectCompany ['$rprs->getCompanyId()'] =  $companyRepo->get($rprs->getCompanyId()) ;
            }
    //------------------------------------------------------------------
    $selectCompany =[];
    $selectLogin =[];    
    
    $companyEntities = $companyRepo->findAll();
        /** @var CompanyInterface $comp */ 
    foreach ( $companyEntities as $comp) {
        $selectCompany [$comp->getId()] =  $comp->getName() ;
    }
    $loginEntities = $loginRepo->findAll();
        /** @var LoginInterface  $logi */ 
    foreach ( $loginEntities as $logi) {
        $selectLogin [] =  $logi->getLoginName() ;
    }
     
    $selecty['selectCompanies'] = $selectCompany;
    $selecty['selectLogins']   = $selectLogin;   
        
  ?>

    <div>
        
        <p> <?= Html::select("jmeno-mesta", "To je label Město:",
            [1=>"", 2=>"Plzeň-město", 3=>"Plzeň-jih", 4=>"Plzeň-sever", 5=>"Klatovy", 6=>"Cheb", 7=>"jiné"],
            ["jmeno-mesta"=>"Plzeň-sever"], []) ?></p>
        
        <p> <?= Html::select("selectCompany", "Company name:",
            $selectCompany,
            ["selectCompany"=>"dzk"], []) ?></p>     
        
        <p> <?= Html::select("selectLogin", "Login name:",
            $selectLogin,
            ["selectLogin"=>"user22"], []) ?></p>      
        


        
        <div >
            Representi vystavovatelů             
            <div class="ui styled fluid accordion">      
                <?= $this->repeat(__DIR__.'/content/representative.php', $representatives  )  ?>
            </div>
            <p></p>

            Přidej dalšího representanta
            <div class="ui styled fluid accordion">            
                    <?= $this->insert( __DIR__.'/content/representative.php',$selecty ) ?>                     
            </div>            
        
        </div>
        

    </div>

 

