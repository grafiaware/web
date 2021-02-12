<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>

            <div class="column">
                <div class="ui segment">
                    <section>
                        <form>
                        <content>
                            <p class="text velky vlevo"><a class="" href="<?= $stanekVystavovateleOdkaz ?>" target="_blank"><?= $nazevVystavovatele ?></a></p>
                            <?= $this->repeat(__DIR__.'/vystavovatel/vyhoda-pro-zamestnance.php', $vyhodyProZamestnance) ?>
                            <p class="text vpravo">
                                <a class="link-img logo-vystavovatele" href="<?= $stanekVystavovateleOdkaz ?>" target="_blank">
                                    <img <?= Html::attributes($imgVystavovateleAttributes)?>/>
                                </a>
                            </p>
                        </content>
                            </form>
                    </section>
                </div>
            </div>

