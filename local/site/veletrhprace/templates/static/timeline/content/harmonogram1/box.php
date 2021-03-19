<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>

    <div class="<?= $boxClass ?>">
        <div class="time">
            <p> <?= $casOD ?> </p>
            <p> <?= $casDO ?> </p>
        </div>
        <i class="<?= $icona ?>"></i>
        <div class="summary">
            <h2><?= $zarazeni ?></h2>
            <p><b><?= $nazev ?></b></p>
            <p>Firma: <?= $firma ?></p>
        </div>
    </div>