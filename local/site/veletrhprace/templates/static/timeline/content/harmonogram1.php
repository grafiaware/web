<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>

<div class="timeline-1">
    <?= $this->repeat(__DIR__.'/harmonogram1/box.php', $timeline1a2) ?>
   
</div>