<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
if ($published) {
?>
<div class="row">
    <div class="box-direction">
        <div class="time">
            <p> <?= $startTime ?> </p>
            <p> <?= $endTime ?> </p>
        </div>
        <i class="<?= $eventType['icon'] ?>"></i>
        <div class="summary">
            <h2><?= $eventType['name'] ?></h2>
            <p><b><?= $title ?></b></p>
            <p> <?= $perex ?> </p>
            <?php if ($linkButtonEnroll['show']) {?>
            <p><a <?= Html::attributes($linkButtonEnroll['linkButtonAttributes']) ?>> <?= $linkButtonEnroll['linkButtonText'] ?> </a></p>
            <?php } ?>
            <?php if ($linkButtonEnter['show']) {?>
            <p><a <?= Html::attributes($linkButtonEnter['linkButtonAttributes']) ?>> <?= $linkButtonEnter['linkButtonText'] ?> </a></p>
            <?php } ?>
        </div>
    </div>
</div>
<?php
}
?>