<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>

            <div class="column">
                <div class="ui violet segment">
                    <section>
                        <form>
                            <content>
                                <div style="text-align: center;">
                                    <a class="logo-vystavovatele" href="<?= $stanekVystavovateleOdkaz ?>" target="_blank">
                                        <img <?= Html::attributes($imgVystavovateleAttributes)?> style="vertical-align:middle; margin-right: 10px;"/>
                                        <i class="linkify large blue icon"></i>
                                    </a>
                                </div>
                                <div class="info-pro-prihlasene">
                                    <?= $this->repeat(__DIR__.'/vystavovatel/info-pro-prihlasene.php', $infoProPrihlasene) ?>
                                </div>
                            </content>
                        </form>
                    </section>
                </div>
            </div>