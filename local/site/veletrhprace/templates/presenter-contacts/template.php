<?php
use Site\Configuration;
use Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;


?>

    <div id="chci-navazat-kontakt">
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
                <?php include "content/kontakty.php" ?>
            </section>
        </article>
    </div>