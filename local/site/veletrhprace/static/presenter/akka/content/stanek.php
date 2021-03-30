<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

?>

                        <div class="ui grid stackable mobile vertically reversed ">
                            <div class="row">
                                <div class="sixteen wide column">
                                    <div class="video-na-stanku">
                                        <content>
                                            <video width="380" height="" <?= Html::attributes($videoAttributes) ?> controls> <!-- u atributu poster nestacilo images/nazev.pripona -->
                                                <?= $this->repeat(__DIR__.'/video/source.php', $videoSourceSrc)?>
                                            </video>
                                        </content>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="ten wide column">
                                    <div class="pani-na-stanku">
                                        <content>
                                            <img <?=Html::attributes($imgStankuAttributes)?> />
                                        </content>
                                    </div>
                                </div>
                                <div class="five wide column middle aligned">
                                    <div class="soc-site-stanku">
                                        <?= $this->repeat(__DIR__.'/sluzby/socialni-site.php', $socialniSiteIframe)?>
                                        <?= $this->insert(__DIR__.'/sluzby/chat.php', $chat) ?>
                                    </div>
                                    <div class="buttony-mimo-stanek">
                                        <content>
                                             <?= $this->repeat(__DIR__.'/sluzby/buttony.php', $buttony)?>
                                        </content>
                                    </div>
                                </div>
                                <div class="sixteen wide column">
                                    <div class="buttony-na-stanku">
                                        <content>
                                             <div class="ui big button red basic btn-letaky" style="background-color: white">Stáhněte si leták</div>
                                        </content>
                                    </div>
                                </div>
                            </div>
                        </div>
