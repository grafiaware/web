<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
if ($published) {
?>

                                    <div class="four wide column">
                                        <div class="timeline-box">
                                            <div class="box-title">
                                                <i class="large <?= $eventType['icon'] ?>"></i> <?= $eventType['name'] ?>
                                            </div>
                                            <div class="box-content">
                                                <div class="box-item"> <?= $title ?></div>
                                                <div class="box-item"></q><small> <?= $perex ?> </small></q></div>
                                                <div class="box-item"><p><b><?= $party ?></b></p></div>
                                                <div class="box-item"><strong><?= Text::resolve($institution['type'], $institution['type'].": ", '') ?></strong> <?= $institution['name'] ?> </div>
                                            </div>
                                            <div class="box-footer"><?= $startTime ?> - <?= $endTime ?></div>
                                            <?php if ($linkButton['show']) {?>
                                            <div><a <?= Html::attributes($linkButton['linkButtonAttributes']) ?>> <?= $linkButton['linkButtonText'] ?> </a></div>
                                            <?php } ?>
                                        </div>
                                    </div>
<?php
}
?>
