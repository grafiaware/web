<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregateInterface;
use Pes\Core\Text\Text;
use Pes\Core\Text\Html;
?>
                                    <div class="eight wide column">
                                        <content>
                                            <video width="100%" height="" <?= Html::attributes($videoAttributes) ?> controls>
                                                <?= $this->repeat(__DIR__.'/video/source.php', $videoSourceSrc)?>
                                            </video>
                                        </content>
                                    </div>    