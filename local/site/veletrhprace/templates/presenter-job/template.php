<?php
use Site\Configuration;
use Model\Arraymodel\EventList; 

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

?>

    <div id="pracovni-pozice">
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
                    <?php include "content/vypis-pozic.php" ?>
                </content>
            </section>
        </article>
    </div>