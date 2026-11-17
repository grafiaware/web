<?php
/** D1: stav návštěvníka v losování (sdílené pro obsluhu). Očekává $st z LotteryService::visitorStatus(). */
$fmt = fn($dt) => (new DateTimeImmutable($dt))->format('j. n. G:i');
?>
<?php if (!$st['joined']): ?>
    <p class="bad">Není zapojen do losování.</p>
<?php else: ?>
    <p>Losů: <b><?= (int) $st['tickets'] ?></b>.
    <?php if ($st['presence'] === null): ?>
        <span class="bad">Los zatím nebyl vydán.</span>
    <?php else: ?>
        Los vydán <?= $fmt($st['presence']['issued']) ?>, platí do <?= $fmt($st['presence']['valid_until']) ?>.
        <?php if ($st['remaining']): ?>
            <span class="ok">Hraje v kolech <?= htmlspecialchars(implode(', ', array_map(fn($r) => (new DateTimeImmutable($r['draw_at']))->format('G:i'), $st['remaining']))) ?>.</span>
        <?php elseif ($st['expired']): ?>
            <span class="bad">Los propadl.</span>
        <?php else: ?>
            <span class="bad">Nezbývá žádné kolo, ve kterém by los hrál.</span>
        <?php endif; ?>
    <?php endif; ?>
    </p>
    <?php foreach ($st['wins'] as $w): ?>
        <p><b><?= $w['status'] === 'forfeited' ? 'Výhra propadla' : 'Výhra' ?>:</b> <?= htmlspecialchars($w['prize_name']) ?>
            (<?= htmlspecialchars(\Events\Service\Lottery\LotteryDrawService::W_STATUSES[$w['status']] ?? $w['status']) ?>)</p>
    <?php endforeach; ?>
<?php endif; ?>
