<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

    $text = 
'
Autorem myšlenky Veletrhu práce a vzdělávání - Klíč k příležitostem a organizátorem akce je <br/><b>Grafia, s.r.o.</b>
 
Umíme efektivně komunikovat akce našich zákazníků i ty vlastní, vzděláváme a zkoušíme dospělé, organizujeme eventy na klíč, vydáváme knihy... <br/> Děláme to už od roku 1993 — rádi a dobře!
';
            

?>

<div class="dva-sloupce-nadpis">
    <p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll">Organizátor</p>
    <div class="ui two column stackable centered mobile reversed grid">
        <div class="six wide column middle aligned">
            <a class="link-img photo-scroll show-on-scroll" href="http://www.grafia.cz" target="_blank"><img src="@siteimages/logo_grafia.png" width="250" height="210" alt="Logo Grafia, s.r.o."/></a>
        </div>
        <div class="ten wide column">
            <?= Html::p(Text::mono($text), ["class"=>"text tucne"]) ?> 
        </div>
    </div>
</div>