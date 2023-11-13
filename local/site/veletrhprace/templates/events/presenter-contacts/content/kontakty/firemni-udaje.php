<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

?>

   
        <div class="row">
            <div class="six wide column middle aligned"><p> <i class="id badge outline icon"></i><?= $kontaktniOsoba ?> <br/> <?= $funkce ?></p></div>
            <div class="eight wide column middle aligned">
                <p><i class="phone icon"></i><?= $telefon ?></p>
                <p><i class="mail icon"></i><a href="mailto:<?= $email ?>"><?= $email ?></a></p>
            </div>
        </div>
        