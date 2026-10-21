<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregateInterface;
use Pes\Core\Text\Text;
use Pes\Core\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

?>

            <div class="column center aligned">
                <div class="letak-stanku">
                    <img <?= Html::attributes($letakAttributes) ?>/>
                    <p style="text-align: center;" class="text maly"><a <?= Html::attributes($downloadAttributes) ?>> Stáhnout leták</a></p>
                </div>
            </div>