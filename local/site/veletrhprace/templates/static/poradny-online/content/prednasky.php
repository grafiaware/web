<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

?>

<div class="prednasejici">
    <div class="ui two column internally celled grid centered">
        <div class="stretched row">
            <div class="eight wide column"><p><b>Název poradny</b></p></div>
            <div class="eight wide column"><p><b>Téma</b></p></div>
        </div>
        <?= $this->repeat(__DIR__.'/prednasky/prednaska.php', $prednaska) ?>
    </div>
</div>
