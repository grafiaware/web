<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
if ($published) {
?>

                                    <div class="sixteen wide mobile eight wide tablet four wide computer column">
                                        <div class="timeline-box">
                                            <div class="box-title">
                                                <i class="<?= $eventType['icon'] ?>"></i> <?= $eventType['name'] ?>
                                            </div>
                                            <div class="box-content">
                                                <div class="box-item"><p class="box-name"> <?= $title ?></p></div>
                                                <div class="box-item"><p class="box-description"> <?= $perex ?> </p></div>
                                                <div class="box-item">
                                                    <p class="box-name"><?= $party ?></p>
                                                    <p class="box-description bold"><strong><?= Text::resolve($institution['type'], $institution['type'].": ", '') ?></strong> <?= $institution['name'] ?> </p>
                                                </div>
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
