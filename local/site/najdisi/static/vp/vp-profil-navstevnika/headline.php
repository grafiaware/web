<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>

<!--class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll"-->
<p><?= Text::mono($headline ?? '') ?></p>