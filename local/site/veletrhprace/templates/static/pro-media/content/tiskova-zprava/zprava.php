<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

?>

        <div class="sixteen wide column">
            <p class="text">
                <?= Text::mono($idZpravy)?>
            </p>
            <p class="podnadpis primarni-barva"> <?= Text::mono($nazev)?></p>
            <?=  Html::p(Text::mono($text), ["class"=>"text"]) ?>
            <hr/>
        </div>