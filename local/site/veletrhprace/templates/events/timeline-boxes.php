<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

?>

<article class="paper">
    <section>
        <headline>
            <?php include "timeline-boxes/headline.php" ?>
        </headline>
        <perex>
            <?php include "timeline-boxes/perex.php" ?>
        </perex>
    </section>
    <section>
        <content>
         <?php include 'timeline-boxes/content/timeline.php' ?>
        </content>
        <content>
            <?php include "timeline-boxes/footer.php" ?>
        </content>
    </section>
</article>