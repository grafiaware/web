<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>

<div class="blok-sedy-obr-text">
    <div class="ui two column grid">
        <div class="ten wide column">
            <div class="blokEL-horni-img photo-scroll show-on-scroll">
                <img src="@images/pan_s_pocitacem.jpg" width="670" height="500" alt="Obrázek"/>
            </div>
        </div> 
        <div class="six wide column">
            <div class="blok-sedy-dole">
                <p class="text tucne">
                    <?= Text::mono('Vzhledem k současné covidové
                                    situaci, nepřející osobním
                                    setkáním, nabízí devátý
                                    ročník nově i <span class="primarni-barva text">online</span> variantu
                                    veletrhu, na které se právě
                                    nacházíte.') ?>
                </p>
            </div>
        </div>
    </div>
</div>
