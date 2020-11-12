<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>

<div class="blok-nadpis-obr-text">
    <div class="ui stackable centered grid">
        <div class="sixteen wide column">
            <div class="primarni-barva podklad nadpis vlevo">
                <p>O akci</p>
            </div>
            <img src="files/pexels-photo-web-orez.jpg" width="1178" height="502" alt="Obrázek"/>
            <div class="velky text vpravo">
                <p>
                    <?= $this->mono('V poslední době pod vlivem Covidu-19 lidé zanedbávali preventivní návštěvy lékařů, odsouvali i nutná ošetření a kontroly. Vlivem médií došlo u řady lidí k úzkostným a iracionálním reakcím, vedoucím až ke škodám na zdraví.') ?>
                </p>
            </div>
        </div>
    </div>
</div>

