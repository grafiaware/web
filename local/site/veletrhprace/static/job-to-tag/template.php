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

        $role = $loginAggregate->getCredentials()->getRole() ?? '';
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
        $jobTagEntities = $jobTagRepo->findAll();
        /** @var JobTagInterface  $jobTagEntity */
        foreach ( $jobTagEntitiesAll as $jobTagEntity) {
            $allTags[$jobTagEntity->getTag()] = [$jobTagEntity->getTag() => $jobTagEntity->getId()] ;


            $allTagsStrings[ $jobTagEntity->getId() ] = $jobTagEntity->getTag();
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
                    $checkTagsStrings=[];
                    foreach ($jobToTagEntities_proJob as $jobToTagEntity) {
                        /** @var JobToTagInterface $jobToTagEntity */
                    // $i = $jobToTagEntity->getJobId(); // $job = $jobRepo->get($i);
                     //   $checkTags[$jobToTagEntity->getJobTagTag()] = $jobToTagEntity->getJobTagTag() ;

                      $idDoTag = $jobToTagEntity->getJobTagId();
                       /** @var JobTagInterface $tagE */
                      $tagE = $jobTagRepo->get($idDoTag);

                      $checkTags[$tagE->getTag()] = $tagE->getId()  ;

    //                  $checkTags[$jobToTagEntity->getJobTagId()] = $jobToTagEntity->getJobTagId() ;
                      $checkTagsStrings[$jobToTagEntity->getJobTagId()] = $tagE->getTag() ;

                    }
                    $jobToTagies[] = [
                            'jobId' => $jobEntity->getId(),
                            'jobNazev' => $jobEntity->getNazev(),
                            'allTags'=>$allTags,
                            'checkTags'=>$checkTags,
                    ];
                }//$jobEntity
            }

  ?>
    <div>
        Vystavovatel (company): |* <?= $companyEntity->getName(); ?> *|
        <br/><br/>
    <div class="ui styled fluid accordion">

        <section>
            Přiřaďte typy k nabízeným pozicím
            <div class="content">
                <?= $this->repeat(__DIR__.'/content/job-to-tag.php',  $jobToTagies  )  ?>
            </div>
            <p></p>
        </section>




        <p> <?= Html::select("jmeno-mesta", "To je label Město:",
            [1=>"", 2=>"Plzeň-město", 3=>"Plzeň-jih", 4=>"Plzeň-sever", 5=>"Klatovy", 6=>"Cheb", 7=>"jiné"],
            ["jmeno-mesta"=>4], []) ?></p>

        <p> <?= Html::select("selectCompany", "Company name:",
            [10=>"Firma10", 25=>"Firma1-město25", 35=>"Firma35", 70=>"jiná"],
            ["selectCompany"=>35], []) ?></p>

        <p> <?= Html::select("selectLogin", "Login name:",
            ["Uzivatel 0", "Uzivatel 1", "Uzivatel 2"],  //index od nuly
            ["selectLogin"=>"Uzivatel 2"], []) ?></p>

        <p> <?= Html::checkbox( [ 'žádné město' => [1=>"" ],
                                  'Plzeň-město' => [2=>"Plzeň-město"],
                                  'Plzeň-jih' => [3=>"Plzeň-jih"],
                                  'Klatovy' => [4=>"Klatovy"] ],
                                [2=>"Plzeň-město"] ) ?></p>


        <?= Html::checkbox(["Label1"=>['technická'=>'technická'],
                            "Label2"=>['manažerská/vedoucí'=>'manažerská/vedoucí']] ,
                           ['manažerská/vedoucí'=>'manažerská/vedoucí']  ) ?>
        <br/>
        <?= MojeHTML::checkbox( ["Label1"=>['technická'=>'technická'],
                                 "Label2"=>['manažerská/vedoucí'=>'manažerská/vedoucí']] ,
                                ['technická'=>'technická'] ) ?>

    </div>
    </div>



  <?php
        }
  ?>

