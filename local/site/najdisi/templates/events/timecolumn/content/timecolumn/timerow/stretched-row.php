<?php
/**
 * Řádek eventu v tabulkovém programu (poradny; data: ProgramViewModel, G0 #116). Hodnoty z DB se escapují zde.
 */
$h = fn($v) => htmlspecialchars((string) $v);
$attrs = fn(array $a) => implode(' ', array_map(fn($k, $v) => $k . '="' . htmlspecialchars((string) $v) . '"', array_keys($a), $a));
if ($published) {
?>
        <div class="stretched row">
            <div class="eight wide column">
                <i class="<?= $h($eventType['icon']) ?>"></i>
                <p><b><?= $h($title) ?></b></p>
                <p><?= $h($startTime) ?> – <?= $h($endTime) ?></p>
                <?php if ($linkButtonEnroll['showEnroll']) { ?>
                <p><button type="submit" <?= $attrs($linkButtonEnroll['linkButtonAttributes']) ?> name="event_enroll"
                    value="<?= (int) $eventId ?>" formaction="events/v1/enroll"><?= $h($linkButtonEnroll['linkButtonText']) ?></button></p>
                <?php } ?>
                <?php if ($linkButtonEnter['showEnter']) { ?>
                <p><a <?= $attrs($linkButtonEnter['linkButtonAttributes']) ?>><?= $h($linkButtonEnter['linkButtonText']) ?></a></p>
                <?php } ?>
            </div>
            <div class="eight wide column">
                <p><b><?= $h($party) ?></b></p>
                <p><?= $h($institution['name']) ?></p>
                <p><?= nl2br($h($perex)) ?></p>
            </div>
        </div>
<?php
}
