<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

$static_ref = '_www_vp_files/presenter/';
$pozice_ref = '/assets/pracovni-pozice.xhtml';
//$pozice_ref = '/assets/pracovni-pozice - kopie.xhtml';

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
        <content>
            <div class="prac-pozice-tab">
                <?php include $static_ref.'akka'.$pozice_ref ?>
                <?php // include $static_ref.'dzk'.$pozice_ref ?>
                <?php include $static_ref.'grafia'.$pozice_ref ?>
                <?php include $static_ref.'kermi'.$pozice_ref ?>
                <?php include $static_ref.'konplan'.$pozice_ref ?>
                <?php include $static_ref.'mdelektronik'.$pozice_ref ?>
                <?php // include $static_ref.'possehl'.$pozice_ref ?>
                <?php // include $static_ref.'stoelzle'.$pozice_ref ?>
                <?php // include $static_ref.'up'.$pozice_ref ?>
                <?php include $static_ref.'valeo'.$pozice_ref ?>
                <?php include $static_ref.'wienerberger'.$pozice_ref ?>
            </div>
        </content>
    </section>
</article>