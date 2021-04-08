<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

$headline = 'Klíč k příležitostem<sup class="text maly">&reg;</sup>';

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
            <?php include "content/videonavody.php" ?>
        </content>
        <content>
            <?php include "content/program.php" ?>
        </content>
        <content>
            <?php include "content/o-akci.php" ?>
        </content>
        <content>
            <?php include "content/o-akci2.php" ?>
        </content>
        <content>
            <?php include "content/o-akci3.php" ?>
        </content>
        <content>
            <?php include "content/veletrh-nabizi.php" ?>
        </content>
        <content>
            <?php include "content/partneri.php" ?>
        </content>
        <content>
            <?php include "content/organizator.php" ?>
        </content>
    </section>
</article>
