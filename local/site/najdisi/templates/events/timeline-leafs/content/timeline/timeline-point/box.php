<?php
/**
 * Řádek eventu v harmonogramu (data: ProgramViewModel, G0 #116). Hodnoty z DB se escapují zde.
 * $showEnrolledCount (z nadřazené šablony) = zobrazit počet přihlášených (harmonogram vystavovatele).
 */
use Pes\View\Renderer\PhpTemplateRendererInterface;

/** @var PhpTemplateRendererInterface $this */
$h = fn($v) => htmlspecialchars((string) $v);
$attrs = fn(array $a) => implode(' ', array_map(fn($k, $v) => $k . '="' . htmlspecialchars((string) $v) . '"', array_keys($a), $a));
if ($published) {
?>
<div class="row">
    <div class="box-direction">
        <div class="time">
            <p><?= $h($startTime) ?></p>
            <p><?= $h($endTime) ?></p>
        </div>
        <i class="<?= $h($eventType['icon']) ?>"></i>
        <div class="summary">
            <h2><?= $h($eventType['name']) ?></h2>
            <p><b><?= $h($title) ?></b></p>
            <p><?= nl2br($h($perex)) ?></p>
            <?php if ($party || $institution['name']) { ?>
                <p><?= $h($party) ?><?= $party && $institution['name'] ? ' – ' : '' ?><?= $h($institution['name']) ?></p>
            <?php } ?>
            <?php if (!empty($showEnrolledCount)) { ?>
                <p>Přihlášeno: <b><?= (int) $enrolledCount ?></b></p>
            <?php } ?>
            <?php if ($linkButtonEnroll['showEnroll']) { ?>
                <div class="text vlevo">
                    <button type="submit" <?= $attrs($linkButtonEnroll['linkButtonAttributes']) ?> name="event_enroll"
                        value="<?= (int) $eventId ?>" formaction="events/v1/enroll"><?= $h($linkButtonEnroll['linkButtonText']) ?></button>
                </div>
            <?php } ?>
            <?php if ($linkButtonEnter['showEnter']) { ?>
                <div class="text vlevo">
                    <a <?= $attrs($linkButtonEnter['linkButtonAttributes']) ?>><?= $h($linkButtonEnter['linkButtonText']) ?></a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php
}
