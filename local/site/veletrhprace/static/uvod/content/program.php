<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>

<div class="blok-nadpis-obr-text program">
    <div class="ui stackable centered grid">
        <div class="sixteen wide column">
            <p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll">Program</p>
            <img src="images/klic_foto.jpg" width="1280" height="420" alt="Obrázek k programu"/>
        </div>
        <div class="eight wide column"><a href="www/item/cs/604bcc5b3c5d7" class="ui primary fluid massive button">Zobrazit přednášky</a></div>
        <div class="eight wide column"><a href="www/item/cs/604bd0e2e440d" class="ui primary fluid massive button">Zobrazit poradny</a></div>
        <div class="sixteen wide column"><a href="" class="ui primary fluid massive button">Online stánky podniků</a></div>
<!--        <div class="sixteen wide column">
                <p class="text tucne">
                    <?
                    Text::mono('V průběhu sedmi dnů vám nabídneme ZDARMA <b>online přednášky a rozhovory s odborníky</b> z různých oborů a také „body pro zdraví“, kde najdete <b>produkty či služby</b> na podporu zdravého životního stylu.') 
                    ?>
                </p>
        </div>-->
    </div>
</div>