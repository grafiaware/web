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
                                            <?php if ($linkButtonEnroll['showEnroll']) {?>
                                                <div class="text vlevo">
                                                    <button type="submit" <?= Html::attributes($linkButtonEnroll['linkButtonAttributes']) ?> name="event_enroll"
                                                        value="<?= $eventId ?>" formtarget="_self"
                                                        formaction="api/v1/event/enroll"> <?= $linkButtonEnroll['linkButtonText'] ?>  </button>
                                                </div>
                                            <?php } ?>
                                            <?php if ($linkButtonEnter['showEnter']) {?>
                                                <div class="text vlevo">
                                                    <a <?= Html::attributes($linkButtonEnter['linkButtonAttributes']) ?>> <?= $linkButtonEnter['linkButtonText'] ?>  </a>
                                                </div>
                                            <?php } ?>

        </div>
    </div>
</div>
<?php
}
?>
