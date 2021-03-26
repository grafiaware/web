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
                                <p>Chatovat s námi můžete přes Facebook Messenger</p>
                                <p>na adrese: <a href="http://m.me/KonplanCZ" target="_blank">http://m.me/KonplanCZ</a></p>
                            </div>
                        </div>
                    </div>
                    <div class="actions">
                        <a class="ui button" href="http://m.me/KonplanCZ" target="_blank">Přejít na Facebook Messenger</a>
                    </div>
                </div>