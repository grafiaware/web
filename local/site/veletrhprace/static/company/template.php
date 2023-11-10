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
        
        $role = $loginAggregate->getCredentials()->getRoleFk() ?? '';
    }
// ------------------------------------------------
   
    /** @var CompanyRepo $companyRepo */ 
    $companyRepo = $container->get(CompanyRepo::class );
    /** @var RepresentativeRepo $representativeRepo */ 
    $representativeRepo = $container->get(RepresentativeRepo::class );
    /** @var LoginRepo $loginRepo */ 
    $loginRepo = $container->get(LoginRepo::class );
    
               
            
        $companies=[];
        $companyEntities = $companyRepo->findAll();
        if ($companyEntities) {         
            foreach ($companyEntities as $cEntity) {
                /** @var CompanyInterface $cEntity */
                $companies[] = [
                    'companyId' => $cEntity->getId(),
                    'name' =>  $cEntity->getName(),
                    'eventInstitutionName30' =>  $cEntity->getEventInstitutionName30()                  
                    ];
            }   
        }             
        
  ?>


    <div>
    <div class="ui styled fluid accordion">   

            <div class="active title">
                <i class="dropdown icon"></i>
                Vystavovatelé (companies)
            </div>     
        
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/company.php',  $companies)  ?>

                <div class="active title">
                    <i class="dropdown icon"></i>
                    Přidej dalšího  vystavovatele
                </div>  
                <div class="active content">     
                    <?= $this->insert( __DIR__.'/company.php') ?>                                                                                 
                </div>                  
            </div>            
    </div>
    </div>

  