<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

?>
                    <content>
                        <span class="<?= $btnClass ?>" data-red-modal-content-id="12"><i class="big <?= $ikonaSocialniSite ?> grey icon"></i></span>

                        <div id="<?= $modalID ?>" class="ui tiny modal">
                            <i class="close icon"></i>
                            <div class="header">
                                Jsme na sociální síti <?= $nazevSocialniSite?>!
                            </div>
                            <div class="content">
                                <div class="ui grid centered">
                                    <?= $iframe ?>
                                </div>
                            </div>
                            <div class="actions">
                                <a class="ui button" href="<?= $odkazNaProfil ?>" target="_blank">Přejít na <?= $nazevSocialniSite?></a>
                            </div>
                        </div>
                    </content>

