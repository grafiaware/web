<?php
use Template\Compiler\TemplateCompilerInterface;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregateInterface;
use Pes\Text\Html;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

use Events\Middleware\Events\ViewModel\JobViewModel;
include 'data.php';

/** @var JobViewModel $jobModel */
$jobModel = $container->get( JobViewModel::class );

//TODO: odstranit předávání kontejneru - potřebuje ho vypis-pozic\pozice_2.php
foreach ($jobModel->getCompanyJobList($shortName) as $job) {
    $jobs[] = array_merge($job, ['container' => ${TemplateCompilerInterface::VARNAME_CONTAINER}, 'shortName' => $shortName]);
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
    <?php include "pracovni-pozice/template.php" ?>
    <?php include "nas-program/template.php" ?>
    <?php include "chci-na-online-pohovor/template.php" ?>
    <?php include "chci-navazat-kontakt/template.php" ?>
    <?php include "stahnout-letak/template.php" ?>
</article>
