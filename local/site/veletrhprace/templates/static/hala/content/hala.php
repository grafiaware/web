<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>

    <div class="online-stanky">
        <div class="ui stackable centered grid">
            <?= $this->repeat(__DIR__.'/hala/rozvrzeni-vystavovatelu.php', $exhibitor) ?>
        </div>
    </div>

