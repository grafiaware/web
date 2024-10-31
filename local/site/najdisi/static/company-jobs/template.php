<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\JobRepo;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Repository\PozadovaneVzdelaniRepo;
use Events\Model\Repository\PozadovaneVzdelaniRepoInterface;

use Events\Model\Entity\CompanyInterface;
use Events\Model\Entity\Job;
use Events\Model\Entity\JobInterface;
use Events\Model\Entity\PozadovaneVzdelani;
use Events\Model\Entity\PozadovaneVzdelaniInterface;
use Component\ViewModel\StatusViewModelInterface;
use Component\ViewModel\StatusViewModel;

/** @var PhpTemplateRendererInterface $this */


    /** @var CompanyRepoInterface $companyRepo */ 
    $companyRepo = $container->get(CompanyRepo::class );
    /** @var JobRepoInterface $jobRepo */
    $jobRepo = $container->get(JobRepo::class );
    /** @var PozadovaneVzdelaniRepoInterface $pozadovaneVzdelaniRepo */
    $pozadovaneVzdelaniRepo = $container->get(PozadovaneVzdelaniRepo::class );
  
   //------------------------------------------------------------------
    /** @var StatusViewModelInterface $statusViewModel */
    $statusViewModel = $container->get(StatusViewModel::class);
    $representativeFromStatus = $statusViewModel->getRepresentativeActions()->getRepresentative();
    $loginName = isset($representativeFromStatus) ? $representativeFromStatus->getLoginLoginName() : null;
    $idCompany = isset($representativeFromStatus) ? $representativeFromStatus->getCompanyId() : null ; 
    //---------- $idCompany je company prihlaseneho representanta
          
    
    if ( isset($idCompany) ) {
                 
            $selectEducations = [];
            $selectEducations [''] =  "vyber - povinné pole" ;
            $vzdelaniEntities = $pozadovaneVzdelaniRepo->findAll();
            /** @var PozadovaneVzdelaniInterface $vzdelaniEntity */ 
            foreach ( $vzdelaniEntities as $vzdelaniEntity ) {
                $selectEducations [$vzdelaniEntity->getStupen()] =  $vzdelaniEntity->getVzdelani() ;
            }  
        
        /** @var CompanyInterface $company */ 
        $company = $companyRepo->get($idCompany);     
            
        $companyJobEntities = $jobRepo->find( " company_id = :idCompany ",  ['idCompany'=> $idCompany ] );
        $companyJobs=[];
                 /** @var JobInterface $jEntity */ 
        foreach ($companyJobEntities as $jEntity) {               
            /** @var PozadovaneVzdelaniInterface $vzdelaniEntity */
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
        
        Vyžaduje přihlášení.    <?= isset($loginName)? " - přihlášen $loginName " : "" ; ?>   <br/>        
        Firma (company): |*     <?= isset($company)? $company->getName() : "" ; ?> *|     
           
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
            Vyžaduje přihlášení.    <?= isset($loginName)? " - přihlášen $loginName " : "" ; ?>   <br/>        
            Firma (company): |*     <?= isset($company)? $company->getName() : "" ; ?> *|         
          </div>   
  <?php 
   }
  ?>