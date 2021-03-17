<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>

            <div class="column">
                <div class="ui segment">
                    <section>
                        <form>
                            <content>
                                <p class="text velky vlevo"><a class="" href="<?= $stanekVystavovateleOdkaz ?>" target="_blank"><?= $nazevVystavovatele ?></a></p>
                                <div class="info-pro-prihlasene">
                                    <?= $this->repeat(__DIR__.'/vystavovatel/info-pro-prihlasene.php', $infoProPrihlasene) ?>
                                </div>
                                <a class="link-img logo-vystavovatele" href="<?= $stanekVystavovateleOdkaz ?>" target="_blank">
                                    <img <?= Html::attributes($imgVystavovateleAttributes)?>/>
                                </a>
                            </content>
                        </form>
                    </section>
                </div>
            </div>

