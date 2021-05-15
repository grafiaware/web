<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

$text = 
        'Hledáte práci?
            
        Poohlížíte se po lepším místě?

        Vítejte na <span class="primarni-barva text">online veletrhu</span>,

        kde to žije vzděláváním

        a pracovními nabídkami!' 
?>
<div class="blok-sedy-nadpis-obr-text">
    <div class="ui two column grid ">
        <div class="sixteen wide column">
            <p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll">O akci</p>
        </div>
        <div class="six wide column">
            <div class="blok-sedy-nahore">
                <?= Html::p(Text::mono($text), ["class"=>"text tucne okraje-horizontal"]) ?>
            </div>
        </div>
        <div class="ten wide column">
            <div class="blokEL-dolni-img photo-scroll show-on-scroll">
                <img src="@images/delnik_na_stroji.jpg" width="670" height="500" alt="Obrázek"/>
            </div>
        </div>
    </div>
</div>
