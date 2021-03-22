<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>

<!--                        <div class="timeline-section">-->
<!--                            <div class="timeline-date">
                                <?= $timelinePoint ?>
                            </div>-->
                            <div class="ui grid">
                                <div class="row">
                                    <?= $this->repeat(__DIR__.'/timeline-point/box.php', $box) ?>
                                </div>
                            </div>
                        <!--</div>-->