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
    /** @var PozadovaneVzdelani $pozadovaneVzdelaniRepo */
    $pozadovaneVzdelaniRepo = $container->get(PozadovaneVzdelani::class );
    
    
    
    //------------------------------------------------------------------

    $idCompany = 25;
    //------------------------------------------------------------------
    
    $selectVzdelani=[];
    $vzdelaniEntities = $pozadovaneVzdelaniRepo->findAll();
    /** @var PozadovaneVzdelaniInterface $vzdelaniEntity */ 
    foreach ( $vzdelaniEntities as $vzdelaniEntity ) {
        $selectVzdelani [$vzdelaniEntity->getStupen()] =  $vzdelaniEntity->getVzdelani() ;
    }

   
    
    tady jsem
    
    
    
    
    /** @var CompanyInterface $companyEntity */ 
    $companyEntity = $companyRepo->get($idCompany);
    if (isset ($companyEntity)) {       
            
        $companyJobEntities = $jobRepo->find( " company_id = :idCompany ",  ['idCompany'=> $idCompany ] );
        $companyJobs=[];
        foreach ($companyJobEntities as $jEntity) {
            /** @var JobInterface $jEntity */     
            $vzdelani = $pozadovaneVzdelaniRepo->getVzdelani( $jEntity->getPozadovaneVzdelaniStupen() );
                                                            
            
            $companyJobs[] = [
                'jobId' => $jEntity->getId(),
                'companyId' => $jEntity->getCompanyId(),
                //'pozadovaneVzdelaniStupen' => $pozadovaneVzdelaniRepo->getVzdelani( $jEntity->getPozadovaneVzdelaniStupen()  ,
                'pozadovaneVzdelani' => $pozadovaneVzdelaniRepo->getVzdelani( $jEntity->getPozadovaneVzdelaniStupen() ) ,
                'nazev' =>  $jEntity->getNazev(),
                
                'mistoVykonu' =>  $jEntity->getMistoVykonu(),
                'popisPozice' =>  $jEntity->getPopisPozice(),
                'pozadujeme' =>  $jEntity->getPozadujeme(),
                'nabizime' =>  $jEntity->getNabizime()
                ];
        }   
            
        
  ?>


    <div>
    <div class="ui styled fluid accordion">   

            Vystavovatel (company): |* <?= $companyEntity->getName(); ?> *|
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
                    <?= $this->insert( __DIR__.'/company-job.php', ['companyId' => $idCompany] ) ?>                                                                                 
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