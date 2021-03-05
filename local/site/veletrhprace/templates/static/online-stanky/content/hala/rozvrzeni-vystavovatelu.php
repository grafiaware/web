<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>

        <div class="stretched row equal width">
            <?= $this->repeat(__DIR__.'/rozvrzeni-vystavovatelu/vystavovatel.php', $radekVystavovatelu) ?>
        </div>

