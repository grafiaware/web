<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */

?>

<article class="paper">
    <section>
        <headline>
            <?php include "timeline-leafs/headline.php" ?>
        </headline>
        <perex>
            <?php include "timeline-leafs/perex.php" ?>
        </perex>
    </section>
    <section>
        <content>
            <?php include "timeline-leafs/content/timeline.php" ?>
        </content>
        <content>
            <?php include "timeline-leafs/footer.php" ?>
        </content>
    </section>
</article>