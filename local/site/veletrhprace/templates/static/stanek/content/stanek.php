<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

?>
                        <div class="video-na-stanku">
                            <content>
                                <video width="330" height="200" <?= Html::attributes($videoAttributes) ?> controls> <!-- u atributu poster nestacilo images/nazev.pripona -->
                                    <source src="<?= $videoSourceSrc?>" type="video/mp4">
                                </video>
                            </content>
                        </div>
                        <div class="ui grid">
                            <div class="ten wide column">
                                <div class="pani-na-stanku">
                                    <content>
                                        <img <?=Html::attributes($imgStankuAttributes)?> />
                                    <content>
                                </div>
                            </div>
                            <div class="five wide column middle aligned">
                                <div class="soc-site-stanku">
                                    <?= $this->repeat(__DIR__.'/stanek/socialni-site.php', $socialniSiteIframe)?>
                                    <?= $this->insert(__DIR__.'/stanek/chat.php', $chat) ?>
                                </div>
                                <div class="buttony-mimo-stanek">
                                    <content>
                                        <p><a class="ui big fluid button" href="www/item/cs/6012ba2fcb683#qqq">Pracovní pozice</a></p>
                                        <p><a class="ui big fluid button" href="">Náš program</a></p>
                                        <p class="btn-12"><span class="ui big fluid button">Chci na živou prezentaci</span></p>
                                        <p><a class="ui big fluid button" href="">Chci na online pohovor</a></p>
                                        <p><a class="ui big fluid button" href="">Chatujte s námi</a></p>
                                        <p><a class="ui big fluid button" href="">Kontaktujte nás</a></p>
                                    </content>
                                </div>
                            </div> 
                            <div class="sixteen wide column">
                                <div class="buttony-na-stanku">
                                    <content>
                                        <div class="ui big button btn-16" >Stáhněte si leták</div>
                                        <a class="ui big button" href="" >Kupóny / dárky</a>
                                    </content>
                                </div>
                            </div>
                        </div>