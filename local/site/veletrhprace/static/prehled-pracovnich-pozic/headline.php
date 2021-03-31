<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>

<p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll"><?= Text::mono($headline) ?></p>