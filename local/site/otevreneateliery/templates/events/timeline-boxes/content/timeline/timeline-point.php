<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
?>

                        <div class="timeline-section">
                            <div class="timeline-date">
                                <?= $timelinePoint ?>
                            </div>
                            <div class="ui grid stackable">
                                <div class="row">
                                    <?= $this->repeat(__DIR__.'/timeline-point/box.php', $box) ?>
                                </div>
                            </div>
                        </div>