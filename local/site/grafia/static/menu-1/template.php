<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

?>
<article class="paper">
    <section>
        <headline>
            <p>dfjhsdkfa</p>
            <?php include "headline.php" ?>
        </headline>
        <perex>
            <?php include "perex.php" ?>
        </perex>
    </section>
    <section>    
        <content>
            <?php include "content/menu.php" ?>
        </content>
    </section>
</article>
