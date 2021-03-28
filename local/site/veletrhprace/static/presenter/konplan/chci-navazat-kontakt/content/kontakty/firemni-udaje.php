<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

?>

    <p class="text velky tucne nastred">Firemní údaje</p>
    <div class="ui grid stackable">
        <div class="row">
            <div class="four wide column middle aligned"><p><?= $kontaktniOsoba ?>, <br/> <?= $funkce ?></p></div>
            <div class="six wide column"><p><i class="phone icon"></i><?= $telefon ?></p><p><i class="mail icon"></i><?= $email ?></p></div>
        </div>
        <div class="row">
            <div class="four wide column middle aligned"><p>Pobočka firmy </div>
            <div class="six wide column"><p><?= $pobockaFirmyUlice ?> <br/><?= $pobockaFirmyMesto ?></p></div> 
        </div>
    </div>