<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Html;

use Pes\Text\Text;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>

<p class=""><?= "";//Text::mono($perex ?? '') ?></p>
<?= Html::p(Text::mono($perex), ["class"=>"text"]) ?>
