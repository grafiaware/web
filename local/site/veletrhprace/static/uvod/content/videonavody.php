<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

$text = 
'

';
?>

<div class="dva-sloupce-nadpis">
    <p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll">Jak na to?</p>
    <div class="ui two column stackable centered mobile reversed grid">
        <div class="six wide column middle aligned">
            <a class="link-img" href="www/item/cs/606eb31a5fc6c" target="_blank">
                <img src="images/videonavody.png" width="" height="300" alt="Videonávody"/>
            </a>
        </div>
        <div class="ten wide column">
            <?= Html::p(Text::mono($text), ["class"=>"text tucne"]) ?> 
            <a class="ui primary massive button" href="www/item/cs/606eb31a5fc6c" target="_blank">Zhlédnout videonávody</a>
        </div>
    </div>
</div>

