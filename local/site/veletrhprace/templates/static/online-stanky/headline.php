<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>

<p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll"><?= Text::mono('Seznam vystavovatelů') ?></p>