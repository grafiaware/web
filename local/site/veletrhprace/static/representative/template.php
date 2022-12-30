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

use Events\Model\Entity\CompanyInterface;
use Events\Model\Entity\CompanyAddressInterface;
use Events\Model\Entity\RepresentativeInterface;

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
    
    $representativeEntities = $representativeRepo->findAll();
    $representatives=[];
            foreach ($representativeEntities as $rprs) {
                /** @var RepresentativeInterface $rprs */
                $representatives[] = [
                    'companyId' => $rprs->getCompanyId(),
                    'loginLoginName' => $rprs->getLoginLoginName(),
                    ];
            }
    //------------------------------------------------------------------


//    $idCompany = 10 ;
//    
//    /** @var CompanyInterface $companyEntity */ 
//    $companyEntity = $companyRepo->get($idCompany);
//    if ($companyEntity) {       
//            
//        $companyAddress=[];
//        /** @var CompanyAddressInterface $companyAddressEntity */
//        $companyAddressEntity = $companyAddressRepo->get($idCompany);
//        if ($companyAddressEntity) {           
//            $companyAddress = [
//                'companyId' => $idCompany,
//                'name'   => $companyAddressEntity->getName(),
//                'lokace' => $companyAddressEntity->getLokace(),
//                'psc'    => $companyAddressEntity->getPsc(),
//                'obec'   => $companyAddressEntity->getObec()
//                ];
//        }    
//        else {
//            $companyAddress = [
//                'companyId' => $idCompany
//                ];
//        }   
            
            
            
            
        $headline="záhlaví";
  ?>

    <div>
        <p class="nadpis"><?= Text::mono($headline) ?></p>
        
        <p>
            <?= Html::select("jmeno-mesta", "To je label Město:",
            [1=>"", 2=>"Plzeň-město", 3=>"Plzeň-jih", 4=>"Plzeň-sever", 5=>"Klatovy", 6=>"Cheb", 7=>"jiné"],
            ["jmeno-mesta"=>"Plzeň-sever"], []) ?></p>
        
        <p> <?=  Html::select("jmeno-mesta1", "To je label:",
                ["", "Plzeň-město", "Plzeň-jih", "Plzeň-sever", "Klatovy", "Cheb", "jiné"],
                ["jmeno-mesta1"=>"Plzeň-sever"], []) ?> </p>
        
        


        
        <div>
        <div class="ui styled fluid accordion">   

            
            <div>                
                Representative vystavovatele 
            </div>                        
            <div>      
                <?= $this->repeat(__DIR__.'/content/representative.php', $representatives  )  ?>

                <div>
                    Přidej další 
                </div>  
                <div class="active content">     
                    <?= $this->insert( __DIR__.'/representative.php' ) ?>                                                                                 
                </div>                  
            </div>            
        </div>
        </div>

  
        
        
        
        
        
        
        
        
        
        
        <div class="ui styled fluid accordion">   
                    
            <div class="active title">
                <i class="dropdown icon"></i>
              Zadej Representative vystavovatele 
              <?= $loginName ?>
            </div>                        
            <div class="active content">      
               <!-- < ?= $this->insert(__DIR__.'/company-address.php',  $companyAddress)  ? >     -->     
            </div>                
        
        </div>

    </div>

 

