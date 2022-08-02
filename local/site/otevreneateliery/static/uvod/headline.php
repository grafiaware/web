<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Pes\Text\Text;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */
?>

<p class="nadpis uvod"><?= Text::mono($headline) ?></p>

