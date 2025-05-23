<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Pes\Text\Text;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */
?>

<div class="blok-nadpis-obr-text program">
    <div class="ui stackable centered grid">
        <div class="sixteen wide column">
            <div class="primarni-barva podklad nadpis vlevo">
                <p>Program</p>
            </div>
            <img src="@images/pexels-polina-web.jpg" width="1178" height="457" alt="Obrázek"/>
        </div>
        <div class="eight wide column"><a href="web/v1/static/prednasky" class="ui secondary fluid massive button">Zobrazit přednášky</a></div>
        <div class="eight wide column"><a href="web/v1/static/body-pro-zdravi" class="ui secondary fluid massive button">Zobrazit místa</a></div>
        <div class="sixteen wide column">
            <div class="velky text vlevo okraje">
                <p>
                    <?= Text::mono('V průběhu sedmi dnů vám nabídneme ZDARMA <b>online přednášky a rozhovory s odborníky</b> z různých oborů a také „body pro zdraví“, kde najdete <b>produkty či služby</b> na podporu zdravého životního stylu.') ?>
                </p>
            </div>
        </div>
    </div>
</div>