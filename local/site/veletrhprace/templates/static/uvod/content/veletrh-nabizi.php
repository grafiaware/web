<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;
?>


<div class="nadpis-obrazkove-sloupce">
    <p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll">Organizátor</p>
    <div class="ui three column stackable centered grid equal width">
        <div class="column">
            <a class="link-img" href="" target="_blank">
                <div class="pozadi-stanek pozadi-img">
                    <p class="text tucne okraje-horizontal">
                        <?= Text::mono('Navštivte bezpečně z pohodlí
                                        domova online stánky s nabídkami
                                        vystavujících firem') ?> 
                    </p>
                </div>
            </a>
        </div>
        <div class="column">
            <a class="link-img" href="" target="_blank">
                <div class="pozadi-prednasky pozadi-img">
                    <p class="text tucne okraje-horizontal">
                        <?= Text::mono('Zaregistrujte se a domluvte si
                                        online konzultaci, nebo se
                                        účastněte online přednášek.') ?> 
                    </p>
                </div>
            </a>
        </div>
        <div class="column">
            <a class="link-img" href="" target="_blank">
                <div class="pozadi-letak pozadi-img">
                    <p class="text tucne okraje-horizontal">
                        <?= Text::mono('Stáhněte si informační materiály
                                        firem a v případě zájmu
                                        rovnou podejte životopis!') ?> 
                    </p>
                </div>
            </a>
        </div>
    </div>
</div>