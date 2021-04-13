<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Html;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

use Model\Arraymodel\Job;

include 'data.php';

$jobModel = new Job();
foreach ($jobModel->getCompanyJobList($shortName) as $job) {
    $jobs[] = array_merge($job, ['container' => $container, 'shortName' => $shortName]);
}

?>

<article class="paper">
    <section>
        <headline>
            <?= $this->insert(__DIR__.'/headline.php', $firma)?>
        </headline>
        <perex>
            <?php include "perex.php" ?>
        </perex>
    </section>
    <section>
        <?= $this->insert(__DIR__.'/content/stanek.php', $firma)?>
    </section>
</article>
