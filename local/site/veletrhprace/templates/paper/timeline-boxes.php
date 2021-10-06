<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

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
         <?php include 'content/timeline.php' ?>
        </content>
        <content>
            <?php include "footer.php" ?>
        </content>
    </section>
</article>