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
                            <img <?= Html::attributes($letakAttributes) ?> />
                            <p style="text-align: center;"><a <?= Html::attributes($downloadAttributes) ?>> Stáhnout leták</a></p>
                        </div>
                    </div>