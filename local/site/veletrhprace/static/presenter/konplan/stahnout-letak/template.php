<?php
use Site\Configuration;
use Model\Arraymodel\EventList; 

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

?>

<article class="paper">
    <section>
        <headline>
            <?php // include "headline.php" ?>
        </headline>
        <perex>
            <?php // include "perex.php" ?>
        </perex>
    </section>
    <section>    
        <content>
            <?php // include "content/stanek.php" ?>
        </content>
    </section>
</article>