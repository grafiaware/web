<?php
/** D1: obsluha načetla QR fotoaparátem – návštěvník, stav losu a ruční vydání / prodloužení. */
/** @var string $login */
/** @var string $name */
/** @var array $items [['lottery', 'status']] */
/** @var string $returnUrl */
?>
<div class="card">
    <h1><?= htmlspecialchars($name) ?></h1>
    <p class="muted"><?= htmlspecialchars($login) ?></p>
</div>
<?php if (!$items): ?><div class="card"><p>Žádné losování není otevřené.</p></div><?php endif; ?>
<?php foreach ($items as $it): $l = $it['lottery']; $st = $it['status']; ?>
<div class="card">
    <h2><?= htmlspecialchars($l['name']) ?></h2>
    <?php include __DIR__ . '/_status.php'; ?>
    <?php if ($st['joined']): ?>
    <form method="POST" action="events/v1/lottery/<?= (int) $l['id'] ?>/presence" class="row">
        <input type="hidden" name="login" value="<?= htmlspecialchars($login) ?>">
        <input type="hidden" name="return" value="<?= htmlspecialchars($returnUrl) ?>">
        <button type="submit" name="issue" value="1">Vchod: vydat / prodloužit</button>
        <button type="submit" name="issue" value="0" class="secondary">Prodloužit</button>
    </form>
    <?php endif; ?>
</div>
<?php endforeach; ?>
