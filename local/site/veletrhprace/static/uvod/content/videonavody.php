<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

$text = 
'
Podívejte se, jak se registrovat, přihlásit nebo jak vložit životopis k pracovní pozici u vybrané firmy
';
?>

<div class="">
    <p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll">Jak na to?</p>
    <div class="ui two column stackable centered grid">
        <div class="nine wide tablet seven wide computer column middle aligned">
            <a class="link-img" href="www/item/cs/606eb31a5fc6c" target="_blank">
                <img src="images/videonavod-foto.jpg" alt="videonávod" />
            </a>
        </div>
        <div class="seven wide tablet nine wide computer column">
            <?= Html::p(Text::mono($text), ["class"=>"text tucne"]) ?> 
            <a class="ui primary massive button" href="www/item/cs/606eb31a5fc6c" target="_blank">Zhlédnout videonávod</a>
        </div>
    </div>
</div>

