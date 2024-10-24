<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

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



    /** @var CompanyRepo $companyRepo */ 
    $companyRepo = $container->get(CompanyRepo::class );
    /** @var JobRepo $jobRepo */
    $jobRepo = $container->get(JobRepo::class );
    /** @var PozadovaneVzdelaniRepo $pozadovaneVzdelaniRepo */
    $pozadovaneVzdelaniRepo = $container->get(PozadovaneVzdelaniRepo::class );
  
    //------------------------------------------------------------------
     //$idCompany = 10; // akka
    //$idCompany = 25 ;  //dzk
    $idCompany = 42 ; 
    
    //------------------------------------------------------------------
    
    $selectEducations = [];
    $selectEducations [''] =  "vyber - povinné pole" ;
    $vzdelaniEntities = $pozadovaneVzdelaniRepo->findAll();
    /** @var PozadovaneVzdelaniInterface $vzdelaniEntity */ 
    foreach ( $vzdelaniEntities as $vzdelaniEntity ) {
        $selectEducations [$vzdelaniEntity->getStupen()] =  $vzdelaniEntity->getVzdelani() ;
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
                'selectEducations' =>  $selectEducations 
                ];
        }   
            
        
  ?>


    <div>
    <div class="ui styled fluid accordion">   
        
        Vyžaduje přihlášení representanta. <br/>
            Firma (company): |* <?= $company->getName(). ' - ' .  $company->getId();  ?> *| 
           
            <div class="active title">
                <i class="dropdown icon"></i>
                Nabízené pracovní pozice ve firmě  <?= $company->getName()  ?>
            </div>                        
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/company-job.php',  $companyJobs)  ?>

                <div class="active title">
                    <i class="dropdown icon"></i>
                    Přidej pracovní pozici
                </div>  
                <div class="active content">     
                    <?= $this->insert( __DIR__.'/company-job.php', ['companyId' => $idCompany, 
                                                                    'selectEducations' =>  $selectEducations ] ) ?>                                                                                 
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
  ?>