<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
/** @var PhpTemplateRendererInterface $this */

?>

<div class="prednasejici">
    <p class="text velky primarni-barva nastred"><?= $timelinePoint ?></p>
    <div class="ui two column internally celled grid centered">
        <div class="stretched row">
            <div class="eight wide column"><p><b>Název přednášky</b></p></div>
            <div class="eight wide column"><p><b>Téma, přednášející</b></p></div>
        </div>
        <?= $this->repeat(__DIR__.'/timecolumn/stretched-row.php', $box) ?>
    </div>
</div>
