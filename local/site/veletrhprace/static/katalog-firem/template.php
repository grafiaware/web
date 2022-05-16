<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\ConfigurationCache;

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
                    <p><a  href="@download/Katalog veletrhPRACE.online 2021.pdf" download="Katalog veletrhPRACE.online 2021">Stáhnout katalog</a></p>
                    <a class="link-img" href="@download/Katalog veletrhPRACE.online 2021.pdf" download="Katalog veletrhPRACE.online 2021">
                        <img  src="@download/Katalog veletrhPRACE.online 2021.jpg" alt="katalog" heigh="" width="60%" />
                    </a>
                </div>
            </div>
        </content>
    </section>
</article>