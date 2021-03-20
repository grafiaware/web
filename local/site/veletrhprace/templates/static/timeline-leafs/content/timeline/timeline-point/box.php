<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
if ($published) {
?>

    <div class="<?= $boxClass ?>">
        <div class="time">
            <p> <?= $startTime ?> </p>
            <p> <?= $endTime ?> </p>
        </div>
        <i class="<?= $eventType['icon'] ?>"></i>
        <div class="summary">
            <h2><?= $eventType['name'] ?></h2>
            <p><b><?= $title ?></b></p>
            <p><q><small> <?= $perex ?> </small></q></p>
            <?php if ($linkButton['show']) {?>
            <p><a <?= Html::attributes($linkButton['linkButtonAttributes']) ?>> <?= $linkButton['linkButtonText'] ?> </a></p>
            <?php } ?>
        </div>
    </div>
<?php
}
?>