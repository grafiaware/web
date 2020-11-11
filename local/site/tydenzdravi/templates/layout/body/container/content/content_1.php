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
            <img src="/_www_tz_files/files/pexels-photo-web-orez.jpg" width="100%" height="" alt="Obrázek"/>
            <div class="velky text vpravo">
                <p>
                    <?= $this->mono('V poslední době pod vlivem Covidu-19 lidé zanedbávali preventivní návštěvy lékařů, odsouvali i nutná ošetření a kontroly. Vlivem médií došlo u řady lidí k úzkostným a iracionálním reakcím, vedoucím až ke škodám na zdraví.') ?>
                </p>
            </div>
        </div>
    </div>
</div>

