<?php
/**
 * D1 (#46): blok Losování v profilu návštěvníka – zapojení s anketou, QR kód, stav losu a výhry.
 * Vkládá template.php jen pro roli visitor při zapnutém přepínači vp.losovani. Očekává $container a $loginName.
 */
use Events\Service\Lottery\LotteryService;
use Events\Service\Lottery\LotteryDrawService;
use Events\Service\Lottery\SurveyValidator;

/** @var LotteryService $lotteryService */
$lotteryService = $container->get(LotteryService::class);
$lotteryItems = [];
foreach ($lotteryService->openLotteries() as $lotteryRow) {
    $lid = (int) $lotteryRow['id'];
    $joined = $lotteryService->isJoined($lid, $loginName);
    if ($joined || $lotteryService->canJoin($lotteryRow)) {
        $lotteryItems[] = ['lottery' => $lotteryRow, 'joined' => $joined, 'canJoin' => $lotteryService->canJoin($lotteryRow),
            'questions' => $lotteryService->questions($lid), 'answers' => $lotteryService->answers($lid, $loginName),
            'status' => $joined ? $lotteryService->visitorStatus($lid, $loginName) : null];
    }
}
$hm = fn($dt) => (new DateTimeImmutable($dt))->format('G:i');
$dm = fn($dt) => (new DateTimeImmutable($dt))->format('j. n. G:i');
?>
<?php if ($lotteryItems): ?>
<div class="ui segment vp-losovani">
    <h3 class="ui header">Losování o ceny</h3>
    <?php foreach ($lotteryItems as $item): $l = $item['lottery']; $st = $item['status']; ?>
        <h4><?= htmlspecialchars($l['name']) ?></h4>
        <?php if (trim((string) $l['description']) !== ''): ?><p><?= nl2br(htmlspecialchars($l['description'])) ?></p><?php endif; ?>

        <?php if (!$item['joined']): ?>
            <form class="ui form" method="POST" action="events/v1/lottery/<?= (int) $l['id'] ?>/join">
                <?php foreach ($item['questions'] as $q): $qid = (int) $q['id']; $opts = SurveyValidator::options($q['options']); ?>
                    <div class="field">
                        <label><?= htmlspecialchars($q['text']) ?><?= (int) $q['required'] ? ' <span style="color:#db2828">*</span>' : '' ?></label>
                        <?php if ($q['type'] === SurveyValidator::TYPE_TEXT): ?>
                            <textarea name="q[<?= $qid ?>]" rows="2" <?= (int) $q['required'] ? 'required' : '' ?>></textarea>
                        <?php else: foreach ($opts as $oi => $o): $oid = 'vp-los-' . (int) $l['id'] . '-q' . $qid . '-' . $oi; ?>
                            <?php /* Fomantic UI: input před label (input:checked ~ label kreslí zaškrtnutí), label s for (#152) */ ?>
                            <div class="ui <?= $q['type'] === SurveyValidator::TYPE_MULTI ? 'checkbox' : 'radio checkbox' ?>" style="display:block; margin:4px 0">
                                <input type="<?= $q['type'] === SurveyValidator::TYPE_MULTI ? 'checkbox' : 'radio' ?>" id="<?= $oid ?>"
                                    name="q[<?= $qid ?>]<?= $q['type'] === SurveyValidator::TYPE_MULTI ? '[]' : '' ?>" value="<?= htmlspecialchars($o) ?>"
                                    <?= ($q['type'] === SurveyValidator::TYPE_SINGLE && (int) $q['required']) ? 'required' : '' ?>>
                                <label for="<?= $oid ?>"><?= htmlspecialchars($o) ?></label>
                            </div>
                        <?php endforeach; endif; ?>
                    </div>
                <?php endforeach; ?>
                <div class="field">
                    <label><input type="checkbox" name="consent" value="1" required>
                        Souhlasím s <?= $l['rules_url'] ? '<a href="' . htmlspecialchars($l['rules_url']) . '" target="_blank" rel="noopener">pravidly losování</a>' : 'pravidly losování' ?></label>
                </div>
                <button class="ui primary button" type="submit">Zapojit se do losování</button>
            </form>
        <?php else: ?>
            <div class="ui grid stackable">
                <div class="six wide column" style="text-align:center">
                    <img src="events/v1/mycode/image" alt="Můj QR kód pro losování" style="width:100%; max-width:220px">
                    <p><a class="ui button" href="events/v1/mycode/page">Můj kód (i bez internetu)</a></p>
                </div>
                <div class="ten wide column">
                    <p><b>Jste zapojeni.</b> Na veletrhu ukažte u vchodu svůj QR kód, obsluha vám vydá los. Los hraje v nejbližších
                        kolech losování; prodloužíte ho na infostánku. <b>Na místě může být slabý signál</b> – uložte si kód
                        do telefonu (obrázek nebo snímek obrazovky) nebo otevřete stránku „Můj kód“.</p>
                    <?php if ($st['presence'] === null): ?>
                        <p>Los zatím nebyl vydán.</p>
                    <?php elseif ($st['remaining']): ?>
                        <p>Los vydán <?= $dm($st['presence']['issued']) ?>. Hrajete v kolech
                            <b><?= htmlspecialchars(implode(', ', array_map(fn($r) => $hm($r['draw_at']), $st['remaining']))) ?></b>
                            (platí do <?= $hm($st['presence']['valid_until']) ?>).</p>
                    <?php elseif ($st['expired']): ?>
                        <p>Los propadl – prodloužíte ho na infostánku, nebo vám ho u vchodu vydají znovu.</p>
                    <?php else: ?>
                        <p>Los platí do <?= $hm($st['presence']['valid_until']) ?>, ale už nezbývá žádné kolo losování, ve kterém by hrál.</p>
                    <?php endif; ?>
                    <?php foreach ($st['wins'] as $w): ?>
                        <?php if ($w['status'] === LotteryDrawService::W_FORFEITED): ?>
                            <p>Výhra <?= htmlspecialchars($w['prize_name']) ?> propadla – nedostavili jste se.</p>
                        <?php elseif ($w['status'] === LotteryDrawService::W_DRAWN): ?>
                            <div class="ui positive message"><b>Vyhráli jste: <?= htmlspecialchars($w['prize_name']) ?>!</b>
                                Přijďte si pro výhru k pódiu. Pokud to na místě nestihnete, ozveme se vám e-mailem.</div>
                        <?php else: ?>
                            <p>Výhra <?= htmlspecialchars($w['prize_name']) ?> – převzato.</p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>
