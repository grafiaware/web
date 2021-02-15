<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>

        <div class="stretched row equal width">
            <?= $this->repeat(__DIR__.'/rozvrzeni-vystavovatelu/vystavovatel.php', $radekVystavovatelu) ?>
        </div>

