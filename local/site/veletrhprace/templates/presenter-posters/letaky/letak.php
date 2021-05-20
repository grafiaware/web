<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

?>

            <div class="column center aligned">
                <div class="letak-stanku">
                    <a <?= Html::attributes($downloadAttributes) ?>><img <?= Html::attributes($letakAttributes) ?>/></a>
                    <p style="text-align: center;" class="text maly"><a <?= Html::attributes($downloadAttributes) ?>> Stáhnout leták</a></p>
                </div>
            </div>