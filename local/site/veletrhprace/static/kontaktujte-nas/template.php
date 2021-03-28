<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

$kontaktniUdaje = [
    [
        'kontaktniOsoba' => 'Radka Novotná',
        'funkce' => 'asistentka',
        'telefon' => '+420 758 659 855',
        'email' => 'firma@firmovata.cz',
        'pobockaFirmyUlice' => 'U velkého poníka 417',
        'pobockaFirmyMesto' => '800 45 Poníkov',
    ]
]

?>
<article class="paper">
<!--    <section>
        <headline>
            <?php include "headline.php" ?>
        </headline>
        <perex>
            <?php include "perex.php" ?>
        </perex>
    </section>-->
    <section>    
        <content>
            <?= $this->repeat(__DIR__.'/content/kontaktni-udaje.php', $kontaktniUdaje) ?>
        </content>
    </section>
</article>