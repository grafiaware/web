<?php
use Pes\Text\Text;
use Pes\Text\Html;

if ($published) {
?>
        <div class="stretched row">

            <div class="eight wide column">
                <i class="<?= $eventType['icon'] ?>"></i>
                <p><b><?= Text::mono($title)?></b></p>
                <p><?= "$startTime - $endTime" ?></p>
                <?php if ($linkButtonEnroll['showEnroll']) {?>
                <p><a <?= Html::attributes($linkButtonEnroll['linkButtonAttributes']) ?>> <?= $linkButtonEnroll['linkButtonText'] ?> </a></p>
                <?php } ?>
                <?php if ($linkButtonEnter['showEnter']) {?>
                <p><a <?= Html::attributes($linkButtonEnter['linkButtonAttributes']) ?>> <?= $linkButtonEnter['linkButtonText'] ?> </a></p>
                <?php } ?>
            </div>
            <div class="eight wide column">
                <p><b><?= $party ?></b></p>
                <p><?= Text::mono($institution['name'])?></p>
                <p><?= Text::mono($perex)?></p>
            </div>
        </div>
<?php
}


