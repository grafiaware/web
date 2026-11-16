<?php
/** D1c: papírové lístky – přehled, vyhledání podle čísla, vytvoření sady, tisk a export (správce). */
/** @var array $lottery */
/** @var array $stats printed, issued, valid, last */
/** @var string $q */
/** @var array|false|null $found null = nehledalo se, false = nenalezeno */
/** @var bool $isAdmin */
/** @var int $perPage */
/** @var int $maxSet */
/** @var int $maxPrint */
use Events\Service\Lottery\LotteryDrawService;
use Events\Service\Lottery\LotteryPaperService;
$id = (int) $lottery['id'];
$h = fn($v) => htmlspecialchars((string) $v);
$tm = fn($v) => $v ? (new DateTimeImmutable($v))->format('G:i') : '–';
?>
<div class="card">
    <h1>Papírové lístky – <?= $h($lottery['name']) ?></h1>
    <p>Vytištěno <b><?= (int) $stats['printed'] ?></b> · vydáno <b><?= (int) $stats['issued'] ?></b> · platných teď <b><?= (int) $stats['valid'] ?></b></p>
    <form method="GET" action="events/v1/lottery/<?= $id ?>/paper" class="row">
        <input type="search" name="q" value="<?= $h($q) ?>" placeholder="číslo lístku (např. 0427)" inputmode="numeric">
        <button type="submit" class="secondary">Hledat</button>
    </form>
</div>

<?php if ($found === false): ?>
<div class="card"><p class="bad">Lístek „<?= $h($q) ?>“ v tomto losování není.</p></div>
<?php elseif ($found): ?>
<div class="card">
    <h2><?= $h($found['label']) ?></h2>
    <?php $p = $found['presence']; ?>
    <?php if ($p === null): ?>
        <p>Los zatím nebyl vydán (lístek se neodevzdal u vchodu).</p>
    <?php else: ?>
        <p>Los vydán <?= $tm($p['issued']) ?>, hraje od <?= $tm($p['eligible_from']) ?>, platí do <?= $tm($p['valid_until']) ?>
            – <?= $found['valid'] ? '<span class="ok">platný</span>' : '<span class="bad">propadlý</span>' ?>.</p>
    <?php endif; ?>
    <?php foreach ($found['wins'] as $w): ?>
        <p><b>Výhra:</b> <?= $h($w['prize_name']) ?> (kolo <?= $tm($w['round_draw_at']) ?>) –
            <?= $h(LotteryDrawService::W_STATUSES[$w['status']] ?? $w['status']) ?></p>
    <?php endforeach; ?>
    <?php if ($isAdmin): ?><p class="muted">Kód: <?= $h(LotteryPaperService::scanCode($found['ticket']['code'])) ?></p><?php endif; ?>
</div>
<?php endif; ?>

<?php if ($isAdmin): ?>
<div class="card">
    <h2>Nová sada lístků</h2>
    <p class="muted">Čísla navazují na poslední lístek<?= $stats['last'] ? ' (' . LotteryPaperService::formatNumber((int) $stats['last']) . ')' : '' ?>.
        Každý lístek dostane náhodný kód do QR na ústřižku.</p>
    <form method="POST" action="events/v1/lottery/<?= $id ?>/paper" class="row">
        <div><label>Počet lístků</label><input type="number" name="count" min="1" max="<?= $maxSet ?>" value="300" required></div>
        <button type="submit">Vytvořit</button>
    </form>
</div>

<?php if ($stats['printed']): ?>
<div class="card">
    <h2>Tisk a export</h2>
    <p class="muted">Tisková stránka se otevře v novém okně; tiskněte na A4 v měřítku 100 % (bez přizpůsobení), případně „Uložit jako PDF“.
        Najednou nejvýš <?= $maxPrint ?> lístků.</p>
    <form method="GET" action="events/v1/lottery/<?= $id ?>/paper/print" target="_blank" class="row">
        <div><label>Od čísla</label><input type="number" name="from" min="1" value="1"></div>
        <div><label>Do čísla</label><input type="number" name="to" min="1" value="<?= min((int) $stats['last'], $maxPrint) ?>"></div>
        <div><label>Na stránku A4</label><select name="per">
            <?php foreach ([4, 2] as $n): ?><option value="<?= $n ?>" <?= $n === $perPage ? 'selected' : '' ?>><?= $n ?> lístky</option><?php endforeach; ?>
        </select></div>
        <button type="submit">Tisková stránka</button>
    </form>
    <p><a class="btn secondary" href="events/v1/lottery/<?= $id ?>/paper/csv">Export CSV (číslo, kód, adresa QR)</a></p>
</div>
<?php endif; ?>
<?php endif; ?>
