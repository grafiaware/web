<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;
?>


<div class="nadpis-obrazkove-sloupce">
    <p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll">Veletrh nabízí...</p>
    <div class="ui three column stackable centered grid equal width">
        <div class="column">
            <a class="link-img" href="www/item/cs/60619d3247985">
                <div class="pozadi-img pozadi-stanek">
                </div>
                <p class="text tucne okraje-horizontal">
                    <?= Text::mono('Navštivte bezpečně z pohodlí
                                    domova online stánky s nabídkami
                                    vystavujících firem.') ?> 
                </p>
            </a>
        </div>
        <div class="column">
            <a class="link-img" href="www/item/cs/604bcc5b3c5d7">
                <div class="pozadi-img pozadi-prednasky">
                </div>
                <p class="text tucne okraje-horizontal">
                    <?= Text::mono('Bohatý program přednášek z veletrhu
                                    můžete i opakovaně zhlédnout  
                                    ze záznamu') ?> 
                    
                </p>
            </a>
        </div>
        <div class="column">
            <a class="link-img" href="www/item/cs/6062d0e00190e">
                <div class="pozadi-img pozadi-letak"></div>
                <p class="text tucne okraje-horizontal">
                    <?= Text::mono('Stáhněte si informační materiály
                                    firem a v případě zájmu
                                    rovnou podejte životopis!') ?> 
                </p>
            </a>
        </div>
    </div>
</div>