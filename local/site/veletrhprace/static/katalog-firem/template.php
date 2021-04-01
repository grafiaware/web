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
                    <p><a  href="_www_vp_files/files/katalog.pdf" download="Katalog firem">Stáhnout katalog</a></p>
                    <a class="link-img" href="_www_vp_files/files/katalog.pdf" download="Katalog firem">
                        <img  src="images/katalog.jpg" alt="katalog" heigh="" width="60%" />
                    </a>
                </div>
            </div>
        </content>
    </section>
</article>