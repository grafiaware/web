<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>

<div class="blok-nadpis-obr-text program">
    <div class="ui stackable centered grid">
        <div class="sixteen wide column">
            <div class="primarni-barva podklad nadpis vlevo">
                <p>Program</p>
            </div>
            <img src="images/pexels-polina-web.jpg" width="1178" height="457" alt="Obrázek"/>
        </div>
        <div class="eight wide column"><a href="www/item/static/prednasky" class="ui secondary fluid massive button">Zobrazit přednášky</a></div>
        <div class="eight wide column"><a href="www/item/static/body-pro-zdravi" class="ui secondary fluid massive button">Zobrazit místa</a></div>
        <div class="sixteen wide column">
            <div class="velky text vlevo">
                <p>
                    <?= $this->mono('V průběhu 1 týdne vám nabídneme <b>přednášky a rozhovory s odborníky</b> z různých oborů a místa, která nabízejí <b>zdravé produkty či služby</b> na podporu zdravého životního stylu. <br/> <b>S kupóny</b> od nás navíc <b>se slevou!</b>') ?>
                </p>
            </div>
        </div>
    </div>
</div>

