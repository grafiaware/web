<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregateInterface;
use Pes\Core\Text\Text;
use Pes\Core\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

?>

                <span class="btn-chat"><i class="big <?= $ikonaChatu ?> grey icon"></i></span>
                
                <div id="modal_chat" class="ui modal">
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