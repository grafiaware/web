<?php
/**
 * Dlaždice eventu v programu (data: ProgramViewModel, G0 #116). Hodnoty z DB se escapují zde.
 * Tlačítko přihlášení odesílá formulář časové osy na events/v1/enroll.
 */
use Pes\View\Renderer\PhpTemplateRendererInterface;

/** @var PhpTemplateRendererInterface $this */
$h = fn($v) => htmlspecialchars((string) $v);
$attrs = fn(array $a) => implode(' ', array_map(fn($k, $v) => $k . '="' . htmlspecialchars((string) $v) . '"', array_keys($a), $a));
if ($published) {
?>
                                    <div class="sixteen wide mobile eight wide tablet four wide computer column">
                                        <div class="timeline-box">
                                            <div class="box-title">
                                                <i class="<?= $h($eventType['icon']) ?>"></i> <?= $h($eventType['name']) ?>
                                            </div>
                                            <div class="box-content">
                                                <div class="box-item"><p class="box-name"><?= $h($title) ?></p></div>
                                                <div class="box-item"><p class="box-description"><?= nl2br($h($perex)) ?></p></div>
                                                <div class="box-item">
                                                    <p class="box-name"><?= $h($party) ?></p>
                                                    <p class="box-description bold"><strong><?= $institution['type'] ? $h($institution['type']) . ': ' : '' ?></strong><?= $h($institution['name']) ?></p>
                                                </div>
                                            </div>
                                            <div class="box-footer"><?= $h($startTime) ?> – <?= $h($endTime) ?></div>
                                            <?php if ($linkButtonEnroll['showEnroll']) { ?>
                                                <div class="text vpravo">
                                                    <button type="submit" <?= $attrs($linkButtonEnroll['linkButtonAttributes']) ?> name="event_enroll"
                                                        value="<?= (int) $eventId ?>" formaction="events/v1/enroll"><?= $h($linkButtonEnroll['linkButtonText']) ?></button>
                                                </div>
                                            <?php } ?>
                                            <?php if ($linkButtonEnter['showEnter']) { ?>
                                                <div class="text vpravo">
                                                    <a <?= $attrs($linkButtonEnter['linkButtonAttributes']) ?>><?= $h($linkButtonEnter['linkButtonText']) ?></a>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
<?php
}
