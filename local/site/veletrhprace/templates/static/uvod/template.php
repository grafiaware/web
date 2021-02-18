<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
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
