<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

$headline = 'Katalog firem';
$perex = '';

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
            <div class="ui grid centered">
                <div class="sixteen wide column center aligned">
                    <a href="" download="">
                        <img src="" alt="katalog" heigh="" width="100%" />
                        <p>Stáhnout katalog</p>
                    </a>
                </div>
            </div>
        </content>
    </section>
</article>