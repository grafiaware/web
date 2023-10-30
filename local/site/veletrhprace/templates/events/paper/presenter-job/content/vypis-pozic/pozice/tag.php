<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

$tag = [
    1 => 'výroba/dělnická',
    2 => 'administrativa/THP',
    3 => 'technická',
    4 => 'manažerská/vedoucí'
];
?>

<span class="ui big red tag label"><?= $tag[$cislo] ?></span>