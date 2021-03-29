<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

?>

    <p class="text velky tucne nastred">Firemní údaje</p>
    <div class="ui grid equal width">
        <div class="row">
            <div class="one wide column middle aligned"><i class="id badge outline icon"></i></div>
            <div class="column"><p><?= $kontaktniOsoba ?>, <br/> <?= $funkce ?></p></div>
        </div>
        <div class="row">
            <div class="one wide column middle aligned"><i class="phone icon"></i></div>
            <div class="column"><p><?= $telefon ?></p></div>
        </div>
        <div class="row">
            <div class="one wide column middle aligned"><i class="mail icon"></i></div>
            <div class="column"><p><?= $email ?></p></div>
        </div>
        <div class="row">
            <div class="one wide column middle aligned"><i class="map outline icon"></i></div>
            <div class="column"><p><?= $pobockaFirmyUlice ?> <br/> <?= $pobockaFirmyMesto ?></p></div>
        </div>
    </div>