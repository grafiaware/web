<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\ConfigurationCache;
use Template\Compiler\TemplateCompilerInterface;

use Events\Model\Arraymodel\JobArrayModel;
use Events\Model\Arraymodel\Presenter;
use Red\Model\Repository\BlockRepo;

use Events\Model\Entity\CompanyInterface;
use Events\Model\Entity\JobInterface;
use Events\Model\Entity\JobToTagInterface;
use Events\Model\Entity\PozadovaneVzdelaniInterface;

$headline = 'Pracovní pozice';
$perex = 'Vítejte v přehledu pracovnich pozic všech vystavovatelů! ';



/** @var Presenter $presenterModel */
$presenterModel = $container->get( Presenter::class );
/** @var JobArrayModel $jobModel */
$jobModel = $container->get( JobArrayModel::class );


// odkaz na stánek - v tabulce blok musí existovat položka s názvem==$shortName
/** @var BlockRepo $blockRepo */
// SVOBODA - čeká na Red databázi - slouží pro generování odkazů na stránku firmy


    foreach ($presenterModel->getCompanyList() as $company ) {

        //TODO: odstranit předávání kontejneru - potřebuje ho vypis-pozic\pozice_2.php
        foreach ($jobModel->getCompanyJobList($company->getId()) as $job) {
            $jobsArray[] = array_merge($job, ['container' => ${TemplateCompilerInterface::VARNAME_CONTAINER}]);
        }

        /** @var CompanyInterface $company */
        $allJobsI[] = [
                //'block' => $block,
                'presenterName' => $company->getName(),
                'presenterJobs' => ['jobs' => $jobsArray],
                ];
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
            <?=  $this->repeat(__DIR__.'/content/presenter-jobs.php', $allJobsI);  ?>

        </content>
    </section>
</article>

