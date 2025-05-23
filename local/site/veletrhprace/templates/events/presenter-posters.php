<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */

?>



<div id="modal_letaky" class="ui modal">
    <i class="close icon"></i>
    <div class="header">
        Letáky ke stažení
    </div>
    <div class="content">
        <div class="ui three column grid centered stackable">
            <?= $this->repeat(__DIR__.'/presenter-posters/letaky/letak.php', $letak) ?>
        </div>
    </div>
    <div class="actions">
    </div>
</div>