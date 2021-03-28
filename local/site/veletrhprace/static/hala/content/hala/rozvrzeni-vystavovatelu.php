<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>


        <div class="three column stretched row">
            <?= $this->repeat(__DIR__.'/rozvrzeni-vystavovatelu/vystavovatel.php', $row) ?>
        </div>

