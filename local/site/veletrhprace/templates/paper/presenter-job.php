<?php
use Site\Configuration;
use Events\Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;

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