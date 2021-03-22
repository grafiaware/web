<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>

<table class="ui definition celled table">
    <thead>
        <tr><th></th>
            <th>Od</th>
            <th>Do</th>
            <th>Název přednášky / pozice</th>
            <th>Firma</th>
        </tr></thead>
    <tbody>
         <?= $this->repeat(__DIR__.'/timetable/radek.php', $timeline1a2) ?>
    </tbody>
</table>