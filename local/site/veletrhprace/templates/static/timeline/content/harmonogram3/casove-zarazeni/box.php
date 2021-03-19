<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>

                                    <div class="four wide column">
                                        <div class="timeline-box">
                                            <div class="box-title">
                                                <i class="large <?= $icona ?>"></i> <?= $zarazeni ?>
                                            </div>
                                            <div class="box-content">
                                                <div class="box-item"><strong>Název přednášky</strong>:<br/> <?= $nazev ?></div>
                                                <div class="box-item"><strong>Firma</strong>: <?= $firma ?> </div>
                                            </div>
                                            <div class="box-footer"><?= $casOD ?> - <?= $casDO ?></div>
                                        </div>
                                    </div>