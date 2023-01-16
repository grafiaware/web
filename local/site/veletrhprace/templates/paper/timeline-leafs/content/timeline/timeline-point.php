<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */
?>

                        <div class="timeline-section">
                            <div class="timeline-date">
                                <?= $timelinePoint ?>
                                </div>
                            </div>

                            <?= $this->repeat(__DIR__.'/timeline-point/box.php', $box) ?>