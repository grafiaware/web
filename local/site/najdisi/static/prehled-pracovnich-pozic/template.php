<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\ConfigurationCache;
use Template\Compiler\TemplateCompilerInterface;

use Events\Middleware\Events\ViewModel\JobViewModel;
use Events\Middleware\Events\ViewModel\RepresentativeViewModel;
use Red\Model\Repository\BlockRepo;

use Events\Model\Entity\CompanyInterface;
use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyRepoInterface;

$headline = 'Pracovní pozice';
$perex = 'Vítejte v přehledu pracovnich pozic všech vystavovatelů! ';




/** @var RepresentativeViewModel $representativeViewModel */
//$representativeViewModel = $container->get( RepresentativeViewModel::class );


/** @var JobViewModel $jobModel */
$jobModel = $container->get( JobViewModel::class );


//TODO: prezentace - link na prezentaci (místo block)
// odkaz na stánek - v tabulce blok musí existovat položka s názvem==$shortName
// príklad:
    /** @var BlockRepo $blockRepo */
//    $blockRepo = $container->get(BlockRepo::class);
//    $shortName = $company->getEventInstitutionName30();
//    $block = isset($shortName) ? $blockRepo->get($shortName) : null;
// SVOBODA - čeká na Red databázi - slouží pro generování odkazů na stránku firmy


     /** @var CompanyRepoInterface $companyRepo */
    $companyRepo = $container->get(CompanyRepo::class);
    $companiesList = $companyRepo->findAll();   
    //$companiesList = $representativeViewModel->getCompanyList();
    
    $allJobs  = [];
    /** @var CompanyInterface $company */
    foreach ($companiesList as $company ) {
        $companyJobs = $jobModel->getCompanyJobList($company->getId());        
        if ($companyJobs) {
                     //TODO: odstranit předávání kontejneru - potřebuje ho vypis-pozic\pozice_2.php            
            $jobsArray = [];
            foreach ( $companyJobs /*$jobModel->getCompanyJobList($company->getId())*/  as $job) {
                $jobsArray[] = array_merge($job, ['container' => ${TemplateCompilerInterface::VARNAME_CONTAINER}], 
                                                 ['companyId' => $company->getId() ]   );
            }
            $allJobs[] = [
                    'block' => null,
                    'companyName' => $company->getName(),
                    'companyJobs' => ['jobs' => $jobsArray],
                    ];        
        }
    }
?>
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

