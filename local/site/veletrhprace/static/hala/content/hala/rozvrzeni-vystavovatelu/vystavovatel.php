<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */
?>

            <div class="column">
                <div class="ui segment">
                    <section>
                        <form>
                            <content>
                                <div style="text-align: center;">
                                    <a class="logo-vystavovatele" href="<?= $urlStand ?>">
                                        <img <?= Html::attributes($logoAttributes)?> style="vertical-align:middle; margin-right: 10px;"/>
                                        <i class="external alternate large blue icon"></i>
                                    </a>
                                </div>
                                <div class="info-pro-prihlasene">
                                    <?= '';  //$this->repeat(__DIR__.'/vystavovatel/info-pro-prihlasene.php', $infoForRegistered); ?>
                                </div>
                            </content>
                        </form>
                    </section>
                </div>
            </div>