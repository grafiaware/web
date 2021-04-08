<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Html;

use Pes\Text\Text;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>
                <div class="ui grid centered">
                    <div class="fifteen wide mobile twelve wide tablet ten wide computer column">
                        <video width="100%" height="" controls <?= Html::attributes($videoAttributes) ?>> 
                            <?= $this->repeat(__DIR__.'/video/source.php', $videoSourceSrc)?>
                        </video>
                    </div>
                </div>