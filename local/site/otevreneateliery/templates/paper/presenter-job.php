<?php
use Site\ConfigurationCache;
use Events\Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;

?>

    <div id="pracovni-pozice">
        <article class="paper">
            <section>
                <headline>
                    <?php include "presenter-job/headline.php" ?>
                </headline>
               <perex>
                    <?php include "presenter-job/perex.php" ?>
                </perex>
            </section>
            <section>
                <content>
                    <?php include "presenter-job/content/vypis-pozic.php" ?>
                </content>
            </section>
        </article>
    </div>