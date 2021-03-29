<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

?>

    <div class="ui grid stackable centered">
        <div class="row">
            <div class="six wide column middle aligned"><p> <i class="id badge outline icon"></i><?= $kontaktniOsoba ?> <br/> <?= $funkce ?></p></div>
            <div class="eight wide column middle aligned"><p><i class="phone icon"></i><?= $telefon ?></p><p><i class="mail icon"></i><a href="mailto:<?= $email ?>"><?= $email ?></a></p></div>
        </div>
        <div class="row">
            <div class="six wide column middle aligned"><p><i class="map outline icon"></i>Pobočka firmy </div>
            <div class="eight wide column middle aligned"><p><i class="map marker icon"></i><?= $pobockaFirmyUlice ?> <br/><?= $pobockaFirmyMesto ?></p></div> 
        </div>
    </div>