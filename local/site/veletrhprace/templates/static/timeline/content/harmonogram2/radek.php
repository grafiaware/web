<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>


        <tr>
            <td><?= $zarazeni ?></td>
            <td><?= $casOD ?></td>
            <td><?= $casDO ?></td>
            <td><?= $nazev ?></td>
            <td><?= $firma ?></td>
        </tr>

