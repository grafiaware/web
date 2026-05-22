<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Pes\Core\Text\Text;
use Pes\Core\Text\Html;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */

?>

        <div class="sixteen wide column">
            <?=  Html::p(Text::mono($idZpravy), ["class"=>"text"])?>
            <?=  Html::p(Text::mono($nazev), ["class"=>"podnadpis primarni-barva"])?>
            <?=  Html::p(Text::mono($text), ["class"=>"text"]) ?>
            <hr/>
        </div>