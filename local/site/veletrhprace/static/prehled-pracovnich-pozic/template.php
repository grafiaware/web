<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\Configuration;

use Model\Arraymodel\Job;
use Model\Arraymodel\Presenter;
use Model\Repository\BlockRepo;


$headline = 'Pracovní pozice';
$perex = '';
$presenterModel = new Presenter();
$jobModel = new Job();

// odkaz na stánek - v tabulce blok musí existovat položka s názvem==$shortName
/** @var BlockRepo $blockRepo */
$blockRepo = $container->get(BlockRepo::class);

foreach ($jobModel->getShortNamesList() as $shortName) {
    $block = $blockRepo->get($shortName);
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