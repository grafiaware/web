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

        $role = $loginAggregate->getCredentials()->getRoleFk() ?? '';
    }

    /** @var CompanyRepoInterface $companyRepo */
    $companyRepo = $container->get(CompanyRepo::class );
    /** @var JobRepoInterface $jobRepo */
    $jobRepo = $container->get(JobRepo::class );
    /** @var JobTagRepoInterface $jobTagRepo */
    $jobTagRepo = $container->get(JobTagRepo::class );
    /** @var JobToTagRepo $jobToTagRepo */
    $jobToTagRepo = $container->get(JobToTagRepo::class );

//    ------------------------------------------------
        $idCompany = 10 ;
//    ------------------------------------------------

        $allTags=[];
        $jobTagEntitiesAll = $jobTagRepo->findAll();
        /** @var JobTagInterface  $jobTagEntity */
        foreach ( $jobTagEntitiesAll as $jobTagEntity) {
            $allTags[$jobTagEntity->getTag()] = [$jobTagEntity->getTag() => $jobTagEntity->getId()] ;
            //$allTagsStrings[ $jobTagEntity->getId() ] = $jobTagEntity->getTag();
        }

        /** @var CompanyInterface $company */
        $company = $companyRepo->get($idCompany);
        if (isset ($company)) {
            // pro company - $idCompany najit vsechny jeji joby
            $jobCompanyEntities = $jobRepo->find( " company_id = :idCompany ",  ['idCompany'=> $idCompany ] );
            if ($jobCompanyEntities) {

                $jobToTagies=[];
                foreach ($jobCompanyEntities as $jobEntity) {
                    /** @var JobInterface $jobEntity */
                    $jobToTagEntities_proJob = $jobToTagRepo->findByJobId( $jobEntity->getId() );

                    $checkTags=[];   //nalepky pro 1 job
                    foreach ($jobToTagEntities_proJob as $jobToTagEntity) {
                       /** @var JobToTagInterface $jobToTagEntity */
                      $idDoTag = $jobToTagEntity->getJobTagId();
                       /** @var JobTagInterface $tagE */
                      $tagE = $jobTagRepo->get($idDoTag);
                      $checkTags[$tagE->getTag()] = $tagE->getId()  ;
                    }
                    $jobToTagies[] = [
                            'jobId' => $jobEntity->getId(),
                            'jobNazev' => $jobEntity->getNazev(),
                            'allTags'=>$allTags,
                            'checkTags'=>$checkTags
                    ];
                }//$jobEntity
            }

  ?>
    <div>
        Nutné přihlášení <br/>
        Vystavovatel (company): |* <?= $company->getName(); ?> *|
        <br/><br/>
    <div class="ui styled fluid accordion">

            <div class="active title">
                 <i class="dropdown icon"></i>
                 Přiřaďte typy k nabízeným pozicím firmy <?= $company->getName(); ?>
            </div>
            <div class="content">
                <?= $this->repeat(__DIR__.'/content/job-to-tag.php',  $jobToTagies  )  ?>
            </div>
            <p></p>
        


    </div>
    </div>



  <?php
        }
  ?>

