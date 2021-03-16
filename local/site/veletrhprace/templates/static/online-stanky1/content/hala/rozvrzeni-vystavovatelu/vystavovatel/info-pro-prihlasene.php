<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>

<p class="text maly zadne-okraje"><i class="<?= $ikona ?> icon link"></i> <?= $popis ?> </p>
