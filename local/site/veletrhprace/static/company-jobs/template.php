<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;
use Auth\Model\Entity\LoginAggregateFullInterface;
use Status\Model\Repository\StatusSecurityRepo;

use Events\Model\Repository\CompanyRepo;
//use Events\Model\Repository\CompanyContactRepo;
use Events\Model\Repository\JobRepo;
use Events\Model\Repository\PozadovaneVzdelaniRepo;

use Events\Model\Entity\CompanyInterface;
//use Events\Model\Entity\CompanyContactInterface;
use Events\Model\Entity\Job;
use Events\Model\Entity\PozadovaneVzdelani;
use Events\Model\Entity\PozadovaneVzdelaniInterface;


/** @var PhpTemplateRendererInterface $this */


$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurity = $statusSecurityRepo->get();
$loginAggregate = $statusSecurity->getLoginAggregate();
if (isset($loginAggregate)) {




    /** @var CompanyRepo $companyRepo */ 
    $companyRepo = $container->get(CompanyRepo::class );
    /** @var JobRepo $jobRepo */
    $jobRepo = $container->get(JobRepo::class );
    /** @var PozadovaneVzdelaniRepo $pozadovaneVzdelaniRepo */
    $pozadovaneVzdelaniRepo = $container->get(PozadovaneVzdelaniRepo::class );
    
    
    
    //------------------------------------------------------------------

    $idCompany = 25;
    //------------------------------------------------------------------
    
    $selectVzdelani=[];
    $vzdelaniEntities = $pozadovaneVzdelaniRepo->findAll();
    /** @var PozadovaneVzdelaniInterface $vzdelaniEntity */ 
    foreach ( $vzdelaniEntities as $vzdelaniEntity ) {
        $selectVzdelanich [$vzdelaniEntity->getStupen()] =  $vzdelaniEntity->getVzdelani() ;
    }

       
    
    
    /** @var CompanyInterface $company */ 
    $company = $companyRepo->get($idCompany);
    if (isset ($company)) {       
            
        $companyJobEntities = $jobRepo->find( " company_id = :idCompany ",  ['idCompany'=> $idCompany ] );
        $companyJobs=[];
        foreach ($companyJobEntities as $jEntity) {
            /** @var JobInterface $jEntity */     
            /** @var PozadovaneVzdelani $vzdelaniEntity */
            $vzdelaniEntity = $pozadovaneVzdelaniRepo->get( $jEntity->getPozadovaneVzdelaniStupen() );
            $vzdelani = $vzdelaniEntity->getVzdelani();
                                                                        
            $companyJobs[] = [
                'jobId' => $jEntity->getId(),
                'companyId' => $jEntity->getCompanyId(),                
                'pozadovaneVzdelaniStupen' =>  $jEntity->getPozadovaneVzdelaniStupen(),
                'nazev' =>  $jEntity->getNazev(),                
                'mistoVykonu' =>  $jEntity->getMistoVykonu(),
                'popisPozice' =>  $jEntity->getPopisPozice(),
                'pozadujeme' =>  $jEntity->getPozadujeme(),
                'nabizime' =>  $jEntity->getNabizime(),                    
                'selectVzdelanich' =>  $selectVzdelanich 
                ];
        }   
            
        
  ?>


    <div>
    <div class="ui styled fluid accordion">   

            Vystavovatel (company): |* <?= $company->getName(); ?> *|
            <div class="active title">
                <i class="dropdown icon"></i>
                Nabízené pracovní pozice 
            </div>                        
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/company-job.php',  $companyJobs)  ?>

                <div class="active title">
                    <i class="dropdown icon"></i>
                    Přidej další pracovní pozici
                </div>  
                <div class="active content">     
                    <?= $this->insert( __DIR__.'/company-job.php', ['companyId' => $idCompany, 
                                                                    'selectVzdelanich' =>  $selectVzdelanich ] ) ?>                                                                                 
                </div>                  
            </div>            
    </div>
    </div>

  <?php     
    } else { ?>
          <div>
          </div>   
  <?php 
   }
   
   
} 
else{
     echo  "Údaje o nabízených pozicích vystavovatele smí vidět jen přihlášený." ; 
}    
   
   
   
  ?>