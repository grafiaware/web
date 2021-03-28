<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

?>

                <span class="btn-56"><i class="big <?= $ikonaChatu ?> grey icon"></i></span>
                
                <div id="modal_56" class="ui modal">
                    <i class="close icon"></i>
                    <div class="header">
                        Jsme pro vás on-line
                    </div>
                    <div class="ui centered grid">
                        <div class="eleven wide column">
                            <div class="text">
                                <?= $text ?>
                            </div>
                        </div>
                    </div>
                    <div class="actions">
                        <?= $odkaz ?>
                    </div>
                </div>