<?php
use Site\ConfigurationCache;
use Events\Model\Arraymodel\EventViewModel;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;


?>

    <div id="chci-navazat-kontakt">
        <article class="paper">
            <section>
                <headline>
                    <?php include "presenter-contacts/headline.php" ?>
                </headline>
                <perex>
                    <?php include "presenter-contacts/perex.php" ?>
                </perex>
            </section>
            <section>
                <?php include "presenter-contacts/content/kontakty.php" ?>
            </section>
        </article>
    </div>