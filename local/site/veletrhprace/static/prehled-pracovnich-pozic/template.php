<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\ConfigurationCache;

use Events\Model\Arraymodel\Job;
use Events\Model\Arraymodel\Presenter;
use Red\Model\Repository\BlockRepo;

use Events\Model\Entity\Company;

$headline = 'Pracovní pozice';
$perex = '';



/** @var Presenter $presenterModel */
$presenterModel = $container->get( Presenter::class );
/** @var Job $jobModel */
$jobModel = $container->get( Job::class );



// odkaz na stánek - v tabulce blok musí existovat položka s názvem==$shortName
/** @var BlockRepo $blockRepo */
// SVOBODA - čeká ba Red databázi - slouží pro generování odkazů na stránku firmy
//
//$blockRepo = $container->get(BlockRepo::class);

foreach ($jobModel->getShortNamesList() as $shortName) {
// SVOBODA - čeká ba Red databázi - slouží pro generování odkazů na stránku firmy
//    
//    $block = $blockRepo->get($shortName);
    $presenterJobs = $jobModel->getCompanyJobList($shortName);
    $jobs = [];
    foreach ($presenterJobs as $job) {
        $jobs[] = array_merge($job, ['container' => $container, 'shortName' => $shortName, 'block' => $block]);  // přidání $container a $shortName pro template pozice
    }
    $allJobs[] = [
                'shortName' => $shortName,
                'presenterName' => $presenterModel->getCompany($shortName)['name'],
                'block' => $block,
                'presenterJobs' => ['jobs' => $jobs],
                'container' => $container
            ];
}



//-------------------------------------------------------------------
    $companyList = $presenterModel->getCompanyListI(); //array
    foreach ($companyList as $companyId=>$company ) {
        /** @var Company $company */
        $allJobsI[] = [
                'shortName' => $company->getId(),
                'presenterName' => $company->getName(),
                'block' => $block,
                'presenterJobs' => ['jobs' => $jobModel->getCompanyJobListI($companyId) ],
                'container' => $container
                 ];
    }
 




?>
 dsvsdvsdvsdvsdv
 
<article class="paper">
    <section>
        <headline>
            <?php include "headline.php" ?>
        </headline>
        <perex>
            <?php include "perex.php" ?>
        </perex>
    </section>
    <section>
        <content class='prehled-pozic'>
            <?=  $this->repeat(__DIR__.'/content/presenter-jobs.php', $allJobs);  ?>
        </content>
    </section>
</article>