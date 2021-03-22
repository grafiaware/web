<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>


        <tr>
            <td><?= $eventType ?></td>
            <td><?= $startTime ?></td>
            <td><?= $endTime ?></td>
            <td><?= $name ?></td>
            <td><?= $institution ?></td>
        </tr>

