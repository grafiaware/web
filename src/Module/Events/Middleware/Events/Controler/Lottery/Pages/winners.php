<?php
/** D1: přehled výher, vyhledání výherce (vyzvednutí v sídle podle e-mailu), převzetí. */
/** @var array $lottery */
/** @var array $winners */
/** @var string $q */
use Events\Service\Lottery\LotteryDrawService;
$id = (int) $lottery['id'];
$back = "events/v1/lottery/{$id}/winners" . ($q !== '' ? '?q=' . rawurlencode($q) : '');
?>
<div class="card">
    <h1>Výhry – <?= htmlspecialchars($lottery['name']) ?></h1>
    <form method="GET" action="events/v1/lottery/<?= $id ?>/winners" class="row">
        <input type="search" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="e-mail výherce (vyzvednutí v sídle) nebo jméno">
        <button type="submit" class="secondary">Hledat</button>
    </form>
</div>
<div class="card">
    <?php if (!$winners): ?><p class="muted">Nic nenalezeno.</p><?php endif; ?>
    <table>
    <?php foreach ($winners as $w): ?>
        <tr>
            <td><b><?= htmlspecialchars($w['display_name']) ?></b><br>
                <span class="muted"><?= htmlspecialchars($w['prize_name']) ?> · kolo <?= $w['round_draw_at'] ? (new DateTimeImmutable($w['round_draw_at']))->format('G:i') : '–' ?> ·
                <?= htmlspecialchars(LotteryDrawService::W_STATUSES[$w['status']] ?? $w['status']) ?></span></td>
            <td style="text-align:right">
                <?php if ($w['status'] === LotteryDrawService::W_DRAWN): ?>
                <form method="POST" action="events/v1/lottery/winner/<?= (int) $w['id'] ?>/claimed" class="inline">
                    <input type="hidden" name="return" value="<?= htmlspecialchars($back) ?>">
                    <button type="submit" name="place" value="misto" class="small">Převzal na místě</button>
                    <button type="submit" name="place" value="sidlo" class="small secondary">Převzal v sídle</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
</div>
