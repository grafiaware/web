<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregateInterface;
use Pes\Core\Text\Text;
use Pes\Core\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

?>

        <div class="row">
            <div class="six wide column middle aligned"><p><i class="map outline icon"></i>Pobočka firmy </div>
            <div class="eight wide column middle aligned"><p><i class="map marker icon"></i><?= $pobockaFirmyUlice ?> <br/><?= $pobockaFirmyMesto ?></p></div> 
        </div>