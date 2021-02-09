<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

    $text = 
'
Autorem myšlenky Týdne zdraví a organizátorem akce je <br/><b>Grafia, s.r.o.</b>
 
Umíme efektivně komunikovat akce našich zákazníků i ty vlastní, vzděláváme a zkoušíme dospělé, organizujeme eventy na klíč, vydáváme knihy... <br/> Děláme to už od roku 1993 – rádi a dobře!
';
            

?>

<div class="dva-sloupce-nadpis">
    <div class="ui two column stackable centered mobile reversed grid">
        <div class="six wide column middle aligned">
            <a class="link-img" href="http://www.grafia.cz" target="_blank"><img src="images/LogoGrafia.jpg" width="300" height="207" alt="Logo Grafia, s.r.o."/></a>
        </div>
        <div class="ten wide column">
            <div class="primarni-barva podklad nadpis vpravo">
                <p>Organizátor</p>
            </div>
            <div class="velky text okraje">
                <?= Html::p(Text::mono($text), ["class"=>""]) ?> 
            </div>

        </div>
    </div>
</div>