<?php
/** D1: nastavení losování – údaje, rozpis kol, ceny, anketa, odkazy pro obsluhu. */
/** @var array $lottery */
/** @var array $rounds */
/** @var array $prizes */
/** @var array $questions */
/** @var array $stations */
/** @var bool $isAdmin */
use Events\Service\Lottery\LotteryService;
use Events\Service\Lottery\SurveyValidator;
$id = (int) $lottery['id'];
$h = fn($v) => htmlspecialchars((string) $v);
$dtl = fn($v) => $v ? (new DateTimeImmutable($v))->format('Y-m-d\TH:i') : '';
$t = fn($v) => (new DateTimeImmutable($v))->format('j. n. Y G:i');
$dis = $isAdmin ? '' : 'disabled';
?>
<div class="card">
    <h1><?= $h($lottery['name']) ?></h1>
    <p class="row">
        <?php foreach ($stations as $key => $s): ?>
            <a class="btn" href="events/v1/lottery/<?= $id ?>/scanner?station=<?= urlencode($key) ?>">Skener: <?= $h($s['label'] ?? $key) ?></a>
        <?php endforeach; ?>
        <a class="btn secondary" href="events/v1/lottery/<?= $id ?>/winners">Výhry</a>
        <a class="btn secondary" href="events/v1/lottery/<?= $id ?>/paper">Papírové lístky</a>
        <?php if ($isAdmin): ?><a class="btn secondary" href="events/v1/lottery/<?= $id ?>/unclaimed/csv">Export nevyzvednutých (CSV)</a><?php endif; ?>
    </p>
</div>

<div class="card">
    <h2>Rozpis kol</h2>
    <?php foreach ($rounds as $r): ?>
    <div style="border-bottom:1px solid #ddd; padding:8px 0">
        <p class="row" style="align-items:center">
            <a class="btn small" style="flex:0 0 auto" href="events/v1/lottery/round/<?= (int) $r['id'] ?>">Losovat <?= (new DateTimeImmutable($r['draw_at']))->format('G:i') ?></a>
            <span class="muted"><?= $r['status'] === 'drawn' ? 'vylosováno ' . $t($r['drawn']) : 'plánováno' ?></span>
        </p>
        <form method="POST" action="events/v1/lottery/<?= $id ?>/round/<?= (int) $r['id'] ?>" class="row">
            <input type="datetime-local" name="draw_at" value="<?= $dtl($r['draw_at']) ?>" required <?= $dis ?>>
            <label class="inline"><input type="checkbox" name="substitute" value="1" <?= (int) $r['substitute'] ? 'checked' : '' ?> <?= $dis ?>>losuje se náhradník</label>
            <?php if ($isAdmin): ?><button type="submit" class="small">Uložit</button><?php endif; ?>
        </form>
        <?php if ($isAdmin && $r['status'] !== 'drawn'): ?>
        <form method="POST" action="events/v1/lottery/<?= $id ?>/round/<?= (int) $r['id'] ?>/remove" onsubmit="return confirm('Smazat kolo?')">
            <button type="submit" class="small secondary">Smazat kolo</button></form>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php if ($isAdmin): ?>
    <form method="POST" action="events/v1/lottery/<?= $id ?>/round" class="row">
        <div><label>Nové kolo</label><input type="datetime-local" name="draw_at" required></div>
        <label class="inline"><input type="checkbox" name="substitute" value="1">losuje se náhradník</label>
        <button type="submit">Přidat kolo</button>
    </form>
    <?php endif; ?>
</div>

<div class="card">
    <h2>Ceny</h2>
    <p class="muted">Cena s kolem se losuje v tom kole, cena bez kola v kterémkoli kole podle pořadí (menší číslo dřív).</p>
    <?php foreach (array_merge($prizes, $isAdmin ? [null] : []) as $p): ?>
    <form method="POST" action="events/v1/lottery/<?= $id ?>/prize<?= $p ? '/' . (int) $p['id'] : '' ?>" class="row">
        <div><label><?= $p ? 'Cena' : 'Nová cena' ?></label><input type="text" name="name" value="<?= $h($p['name'] ?? '') ?>" required <?= $dis ?>></div>
        <div style="flex:0 1 90px"><label>Kusů</label><input type="number" name="quantity" min="1" value="<?= (int) ($p['quantity'] ?? 1) ?>" <?= $dis ?>></div>
        <div style="flex:0 1 90px"><label>Pořadí</label><input type="number" name="priority" value="<?= (int) ($p['priority'] ?? 0) ?>" <?= $dis ?>></div>
        <div><label>Kolo</label><select name="round_id_fk" <?= $dis ?>><option value="0">kterékoli</option>
            <?php foreach ($rounds as $r): ?><option value="<?= (int) $r['id'] ?>" <?= (int) ($p['round_id_fk'] ?? 0) === (int) $r['id'] ? 'selected' : '' ?>>
                <?= (new DateTimeImmutable($r['draw_at']))->format('G:i') ?><?= (int) $r['substitute'] ? ' (náhradník)' : '' ?></option><?php endforeach; ?></select></div>
        <?php if ($isAdmin): ?><button type="submit" class="small"><?= $p ? 'Uložit' : 'Přidat' ?></button><?php endif; ?>
        <?php if ($p): ?><span class="muted">vylosováno <?= (int) $p['won'] ?>×</span><?php endif; ?>
    </form>
    <?php if ($p && $isAdmin && (int) $p['won'] === 0): ?>
        <form method="POST" action="events/v1/lottery/<?= $id ?>/prize/<?= (int) $p['id'] ?>/remove" onsubmit="return confirm('Smazat cenu?')">
            <button type="submit" class="small secondary">Smazat cenu</button></form>
    <?php endif; ?>
    <hr>
    <?php endforeach; ?>
</div>

<div class="card">
    <h2>Anketa</h2>
    <p class="muted">Návštěvník odpoví při zapojení v profilu; povinné otázky jsou podmínkou zapojení. Po prvních odpovědích lze otázku jen přejmenovat.
        Možnosti pište každou na nový řádek.</p>
    <?php foreach (array_merge($questions, $isAdmin ? [null] : []) as $q): $locked = $q && (int) $q['answered'] > 0; ?>
    <form method="POST" action="events/v1/lottery/<?= $id ?>/question<?= $q ? '/' . (int) $q['id'] : '' ?>">
        <div class="row">
            <div style="flex:0 1 80px"><label>Pořadí</label><input type="number" name="position" value="<?= (int) ($q['position'] ?? count($questions) + 1) ?>" <?= $dis ?>></div>
            <div style="flex:3 1 240px"><label><?= $q ? 'Otázka' : 'Nová otázka' ?></label><input type="text" name="text" value="<?= $h($q['text'] ?? '') ?>" required <?= $dis ?>></div>
            <div><label>Typ</label><select name="type" <?= $locked ? 'disabled' : $dis ?>>
                <?php foreach (SurveyValidator::TYPES as $k => $label): ?><option value="<?= $k ?>" <?= ($q['type'] ?? 'single') === $k ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?></select></div>
        </div>
        <label>Možnosti</label><textarea name="options" rows="3" <?= $locked ? 'disabled' : $dis ?>><?= $h($q['options'] ?? '') ?></textarea>
        <label class="inline"><input type="checkbox" name="required" value="1" <?= ($q === null || (int) $q['required']) ? 'checked' : '' ?> <?= $locked ? 'disabled' : $dis ?>>povinná</label>
        <?php if ($isAdmin): ?><button type="submit" class="small"><?= $q ? 'Uložit' : 'Přidat' ?></button><?php endif; ?>
        <?php if ($q): ?><span class="muted">odpovědí <?= (int) $q['answered'] ?></span><?php endif; ?>
    </form>
    <?php if ($q && $isAdmin && !$locked): ?>
        <form method="POST" action="events/v1/lottery/<?= $id ?>/question/<?= (int) $q['id'] ?>/remove" onsubmit="return confirm('Smazat otázku?')">
            <button type="submit" class="small secondary">Smazat otázku</button></form>
    <?php endif; ?>
    <hr>
    <?php endforeach; ?>
</div>

<div class="card">
    <h2>Údaje losování</h2>
    <form method="POST" action="events/v1/lottery/<?= $id ?>">
        <label>Název</label><input type="text" name="name" value="<?= $h($lottery['name']) ?>" required <?= $dis ?>>
        <label>Popis (zobrazí se v profilu)</label><textarea name="description" rows="3" <?= $dis ?>><?= $h($lottery['description']) ?></textarea>
        <label>Odkaz na pravidla</label><input type="url" name="rules_url" value="<?= $h($lottery['rules_url']) ?>" <?= $dis ?>>
        <div class="row">
            <div><label>Zapojit se lze od</label><input type="datetime-local" name="join_from" value="<?= $dtl($lottery['join_from']) ?>" <?= $dis ?>></div>
            <div><label>do</label><input type="datetime-local" name="join_until" value="<?= $dtl($lottery['join_until']) ?>" <?= $dis ?>></div>
        </div>
        <label>Stav</label><select name="status" <?= $dis ?>>
            <?php foreach (LotteryService::STATUSES as $k => $label): ?><option value="<?= $k ?>" <?= $lottery['status'] === $k ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?></select>
        <p class="muted">Zapojit se a skenovat lze jen v otevřeném losování.</p>
        <?php if ($isAdmin): ?><p><button type="submit">Uložit</button></p><?php endif; ?>
    </form>
</div>
