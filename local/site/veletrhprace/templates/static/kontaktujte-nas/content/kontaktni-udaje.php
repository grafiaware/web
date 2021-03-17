<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

?>
<div class="kontaktni-udaje-firmy">
    <p class="nadpis nastred podtrzeny show-on-scroll nadpis-scroll">Kontaktujte nás</p>
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
</div>
<p></p>
<div class="kontaktni-udaje-firmy">
    <p class="nadpis nastred podtrzeny show-on-scroll nadpis-scroll">Kontaktujte nás</p>
    <div class="ui grid">
        <div class="row">
            <div class="four wide column middle aligned"><p><?= $kontaktniOsoba ?>, <br/> <?= $funkce ?></p></div>
            <div class="six wide column"><p><i class="phone icon"></i><?= $telefon ?></p><p><i class="mail icon"></i><?= $email ?></p></div>
        </div>
        <div class="row">
            <div class="four wide column middle aligned"><p>Pobočka firmy </div>
            <div class="six wide column"><p><?= $pobockaFirmyUlice ?> <br/><?= $pobockaFirmyMesto ?></p></div> 
        </div>
    </div>
</div>
