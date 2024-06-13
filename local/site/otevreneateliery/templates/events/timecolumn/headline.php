<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Pes\Text\Text;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */
?>

<p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll">
    <?= Text::mono($headline) ?>
</p>