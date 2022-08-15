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
                <?php if ($linkButton['show']) {?>
                <p><a <?= Html::attributes($linkButton['linkButtonAttributes']) ?>> <?= $linkButton['linkButtonText'] ?> </a></p>
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


