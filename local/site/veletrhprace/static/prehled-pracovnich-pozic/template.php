<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\Configuration;

use Model\Arraymodel\Job;
use Model\Arraymodel\Presenter;



$headline = 'Pracovní pozice';
$perex = '';
$presenterModel = new Presenter();
$jobModel = new Job();

foreach ($jobModel->getShortNamesList() as $shortName) {
    $presenterJobs = $jobModel->getCompanyJobList($shortName);
    $jobs = [];
    foreach ($presenterJobs as $job) {
        $jobs[] = array_merge($job, ['container' => $container, 'shortName' => $shortName]);
    }
    $allJobs[] = [
                'shortName' => $shortName,
                'presenterName' => $presenterModel->getCompany($shortName)['name'],
                'presenterJobs' => ['jobs' => $jobs]
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