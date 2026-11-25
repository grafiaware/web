<?php
/** D1: přehled losování. */
/** @var array $lotteries */
/** @var bool $isAdmin */
use Events\Service\Lottery\LotteryService;
use Events\Service\Lottery\TextLimits;
?>
<div class="card">
    <h1>Losování</h1>
    <?php if (!$lotteries): ?><p class="muted">Zatím žádné losování.</p><?php endif; ?>
    <table>
        <?php foreach ($lotteries as $l): ?>
        <tr>
            <td><a href="events/v1/lottery/<?= (int) $l['id'] ?>"><b><?= htmlspecialchars($l['name']) ?></b></a></td>
            <td><?= htmlspecialchars(LotteryService::STATUSES[$l['status']] ?? $l['status']) ?></td>
            <td><a class="btn small" href="events/v1/lottery/<?= (int) $l['id'] ?>/scanner">Skener</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php if ($isAdmin): ?>
<div class="card">
    <h2>Nové losování</h2>
    <form method="POST" action="events/v1/lottery">
        <label for="name">Název</label><input type="text" id="name" name="name" maxlength="<?= TextLimits::LOTTERY['name'] ?>" required placeholder="např. Veletrh 2027 – na místě">
        <p><button type="submit">Založit</button></p>
    </form>
</div>
<?php endif; ?>
