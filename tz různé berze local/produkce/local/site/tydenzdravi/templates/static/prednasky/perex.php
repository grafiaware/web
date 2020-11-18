<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>

<p class="velky text do-kraju">
    <?= $this->mono('V průběhu 1 týdne budou probíhat přednášky a rozhovory s odborníky z různých oborů. Všechny přednášky jsou <b>on-line</b> a pro účastníky <b>zdarma</b>.')?>
</p>
<p class="velky text do-kraju">
    <?= $this->mono('<a href="https://forms.gle/aUoufuqLyaSjm6rE8" target="_blank">Přihlaste se zdarma</a> na vybranou přednášku. Přihlášeným bude zaslán link na e-mail uvedený v přihlašovacím formuláři. <br/> Máte pro přednášející <a href="https://forms.gle/aUoufuqLyaSjm6rE8" target="_blank">dotazy?</a> Napište je předem, ať máte jistotu, že se na ně dostane.')?>
</p>
