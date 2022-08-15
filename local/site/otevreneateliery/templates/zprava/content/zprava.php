<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Pes\Text\Text;
use Pes\Text\Html;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */

?>

        <div class="sixteen wide column">
            <?=  Html::p(Text::mono($idZpravy), ["class"=>"text"])?>
            <?=  Html::p(Text::mono($nazev), ["class"=>"podnadpis primarni-barva"])?>
            <?=  Html::p(Text::mono($text), ["class"=>"text"]) ?>
            <hr/>
        </div>