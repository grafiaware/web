<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Auth\Model\Entity\LoginAggregateFullInterface;
use Status\Model\Repository\StatusSecurityRepo;

use Pes\Text\Text;
use Pes\Text\Html;

use Events\Model\Repository\JobToTagRepo;
use Events\Model\Repository\JobTagRepo;
use Events\Model\Repository\JobRepo;
use Events\Model\Repository\JobToTagRepoInterface;
use Events\Model\Repository\JobTagRepoInterface;
use Events\Model\Repository\JobRepoInterface;

use Events\Model\Entity\JobToTag;
use Events\Model\Entity\JobToTagInterface;
use Events\Model\Entity\JobTag;
use Events\Model\Entity\JobTagInterface;
use Events\Model\Entity\Job;
use Events\Model\Entity\JobInterface;


use Events\Model\Repository\LoginRepo;
use Events\Model\Entity\LoginInterface;

/** @var PhpTemplateRendererInterface $this */


   $statusSecurityRepo = $container->get(StatusSecurityRepo::class);
    /** @var StatusSecurityRepo $statusSecurityRepo */
    $statusSecurity = $statusSecurityRepo->get();
    $loginAggregate = $statusSecurity->getLoginAggregate();
    if (isset($loginAggregate)) {
        $loginName = $loginAggregate->getLoginName();
        $cred = $loginAggregate->getCredentials();
        
        $role = $loginAggregate->getCredentials()->getRole() ?? '';
    }
//    ------------------------------------------------
    
   
    /** @var JobRepoInterface $jobRepo */ 
    $jobRepo = $container->get(JobRepo::class );
    /** @var JobTagRepoInterface $jobTagRepo */ 
    $jobTagRepo = $container->get(JobTagRepo::class );
    /** @var JobToTagRepo $jobToTagRepo */ 
    $jobToTagRepo = $container->get(JobToTagRepo::class );
        
//    
//    /** @var LoginRepo $loginRepo */ 
//    $loginRepo = $container->get(LoginRepo::class );
    
    
    
    
    $jobToTagEntities = $jobToTagRepo->findAll();
    $jobToTagies=[];    
            foreach ($jobToTagEntities as $jobToTagEntity) {
                /** @var JobToTagInterface $jobToTagEntity */
                $i = $jobToTagEntity->getJobId();
                
                $job = $jobRepo->get( $i);
                
                $jobToTagies[] = [
                    'jobId' => $jobToTagEntity->getJobId(),
                    'jobNazev' => $job->getNazev(),                    
                    'jobTagTag' => $jobToTagEntity->getJobTagTag()                   
                    ];
            }
    //------------------------------------------------------------------
    $selectJobs =[];
    $selectJobTags =[];    
    
    $jobEntities = $jobRepo->findAll();
        /** @var JobInterface $job */ 
    foreach ( $jobEntities as $job) {
        $selectJobs [$job->getId()] =  $job->getNazev() ;
    }
    
    
    $jobTagEntities = $jobTagRepo->findAll();
        /** @var LoginInterface  $logi */ 
    foreach ( $jobTagEntities as $jobTagEntity) {
        $selectJobTags [] =  $jobTagEntity->getTag() ;
    }
     
    $selecty['selectJobs'] = $selectJobs;
    $selecty['selectJobTags']  = $selectJobTags;   
        
  ?>
 
 
        <div>
            Přiřazení typu k nabízeným pozicím
            <div class="ui styled fluid accordion">      
                <?= $this->repeat(__DIR__.'/content/job-to-tag.php', $jobToTagies  )  ?>
            </div>
            <p></p>

            Přidej další
            <div class="ui styled fluid accordion">            
                    <?= $this->insert( __DIR__.'/content/job-to-tag.php',$selecty ) ?>                     
            </div>            
        
        </div>
        
 

