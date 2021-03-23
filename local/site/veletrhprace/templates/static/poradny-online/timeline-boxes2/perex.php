<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

    $text =
    '
Přihlaste se na konkrétní čas v poradnách! (po 21. 3. 2021)
';

    echo Html::p(Text::mono($text), ["class"=>"velky text"]);
?>