<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Auth\Model\Entity\LoginAggregateFullInterface;
use Status\Model\Repository\StatusSecurityRepo;

use Pes\Text\Text;
use Pes\Text\Html;
use Moje\MojeHTML;

use Events\Model\Repository\JobToTagRepo;
use Events\Model\Repository\JobTagRepo;
use Events\Model\Repository\JobRepo;
use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\JobToTagRepoInterface;
use Events\Model\Repository\JobTagRepoInterface;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Repository\CompanyRepoInterface;

use Events\Model\Entity\CompanyInterface;
use Events\Model\Entity\JobToTag;
use Events\Model\Entity\JobToTagInterface;
use Events\Model\Entity\JobTag;
use Events\Model\Entity\JobTagInterface;
use Events\Model\Entity\Job;
use Events\Model\Entity\JobInterface;

use Events\Model\Repository\LoginRepo;
use Events\Model\Entity\LoginInterface;

use Component\ViewModel\StatusViewModelInterface;
use Component\ViewModel\StatusViewModel;

/** @var PhpTemplateRendererInterface $this */
    
    /** @var CompanyRepoInterface $companyRepo */
    $companyRepo = $container->get(CompanyRepo::class );
    /** @var JobRepoInterface $jobRepo */
    $jobRepo = $container->get(JobRepo::class );
    /** @var JobTagRepoInterface $jobTagRepo */
    $jobTagRepo = $container->get(JobTagRepo::class );
    /** @var JobToTagRepoInterface $jobToTagRepo */
    $jobToTagRepo = $container->get(JobToTagRepo::class );

//------------------------------------------------------------------
    /** @var StatusViewModelInterface $statusViewModel */
    $statusViewModel = $container->get(StatusViewModel::class);
    $representativeFromStatus = $statusViewModel->getRepresentativeActions()->getRepresentative();
    $loginName = isset($representativeFromStatus) ? $representativeFromStatus->getLoginLoginName() : null;
    $idCompany = isset($representativeFromStatus) ? $representativeFromStatus->getCompanyId() : null ; 
    //---------------------------------------------        
        
     if ( isset($idCompany) ) {
     
        $allTags=[];
        $jobTagEntitiesAll = $jobTagRepo->findAll();
        /** @var JobTagInterface  $jobTagEntity */
        foreach ( $jobTagEntitiesAll as $jobTagEntity) {
            $allTags[$jobTagEntity->getTag()] = ["data[{$jobTagEntity->getTag()}]" => $jobTagEntity->getId()] ;
            //$allTagsStrings[ $jobTagEntity->getId() ] = $jobTagEntity->getTag();
        }

        /** @var CompanyInterface $company */
        $company = $companyRepo->get($idCompany);
        //if (isset ($company)) {
            // pro company - $idCompany najit vsechny jeji joby
            $jobCompanyEntities = $jobRepo->find( " company_id = :idCompany ",  ['idCompany'=> $idCompany ] );
            if ($jobCompanyEntities) {

                $jobToTagies=[];
                foreach ($jobCompanyEntities as $jobEntity) {
                    /** @var JobInterface $jobEntity */
                    $jobToTagEntities_proJob = $jobToTagRepo->findByJobId( $jobEntity->getId() );

                    $checkedTags=[];   //nalepky pro 1 job
                    foreach ($jobToTagEntities_proJob as $jobToTagEntity) {
                       /** @var JobToTagInterface $jobToTagEntity */
                      $idDoTag = $jobToTagEntity->getJobTagId();
                       /** @var JobTagInterface $tagE */
                      $tagE = $jobTagRepo->get($idDoTag);
                      $checkedTags["data[{$tagE->getTag()}]"] = $tagE->getId()  ;
                    }
                    $jobToTagies[] = [
                            'jobId' => $jobEntity->getId(),
                            'jobNazev' => $jobEntity->getNazev(),
                            'allTags'=>$allTags,
                            'checkedTags'=>$checkedTags
                    ];
                }//$jobEntity
            }
  ?>
    <div>
        
    <div class="ui styled fluid accordion">                 
            Vyžaduje přihlášení.    <?= isset($loginName)? " - přihlášen $loginName " : "" ; ?>   <br/>        
            Firma (company): |*     <?= isset($company)? $company->getName() : "" ; ?> *|             
            
            <div class="active title">
                 <i class="dropdown icon"></i>
                 Přiřaďte typy k nabízeným pozicím firmy <?= $company->getName(); ?>
            </div>
            <div class="content">
                <?= $this->repeat(__DIR__.'/content/job-to-tag.php',  $jobToTagies  )  ?>
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
