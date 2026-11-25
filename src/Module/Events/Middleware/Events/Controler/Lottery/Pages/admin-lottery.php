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
$t = fn($v) => (new DateTimeImmutable($v))->format('j. n. Y G:i');
$dis = $isAdmin ? '' : 'disabled';
// Datum: kalendář type=date (zavře se kliknutím na den); čas: psaný z klávesnice (10:30) – bez vícekrokového dialogu datetime-local.
$dt = function (string $name, ?string $value, string $defaultDate = '', bool $required = false) use ($h, $dis): string {
    $d = $value ? (new DateTimeImmutable($value))->format('Y-m-d') : $defaultDate;
    $tm = $value ? (new DateTimeImmutable($value))->format('G:i') : '';
    $req = $required ? 'required' : '';
    return '<span class="dt"><input type="date" name="' . $name . '_date" value="' . $h($d) . '" ' . $req . ' ' . $dis . ' aria-label="datum">'
        . '<input type="text" class="time" name="' . $name . '_time" value="' . $h($tm) . '" placeholder="10:30" inputmode="numeric"'
        . ' pattern="([01]?[0-9]|2[0-3])([:.]?[0-5][0-9])?" title="čas, např. 10:30" ' . $req . ' ' . $dis . ' aria-label="čas"></span>';
};
$lastDate = $rounds ? (new DateTimeImmutable(end($rounds)['draw_at']))->format('Y-m-d')
    : ($lottery['join_from'] ? (new DateTimeImmutable($lottery['join_from']))->format('Y-m-d') : '');
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
    <p class="muted">Datum vyberte v kalendáři, čas napište (např. 10:30).</p>
    <?php foreach ($rounds as $r): ?>
    <div class="card">
        <p class="row" style="align-items:center">
            <a class="btn small" style="flex:0 0 auto" href="events/v1/lottery/round/<?= (int) $r['id'] ?>">Losovat <?= (new DateTimeImmutable($r['draw_at']))->format('G:i') ?></a>
            <span class="muted"><?= $r['status'] === 'drawn' ? 'vylosováno ' . $t($r['drawn']) : 'plánováno' ?></span>
        </p>
        <form method="POST" action="events/v1/lottery/<?= $id ?>/round/<?= (int) $r['id'] ?>" class="row">
            <div><label>Datum a čas</label><?= $dt('draw_at', $r['draw_at'], '', true) ?></div>
            <label class="inline"><input type="checkbox" name="substitute" value="1" <?= (int) $r['substitute'] ? 'checked' : '' ?> <?= $dis ?>>losuje se náhradník</label>
            <?php if ($isAdmin): ?><button type="submit" class="small">Uložit</button><?php endif; ?>
        </form>
        <?php if ($isAdmin): ?>
        <form method="POST" action="events/v1/lottery/<?= $id ?>/round/<?= (int) $r['id'] ?>/copy" class="row">
            <div><label>Duplikát kola s cenami kola</label><?= $dt('draw_at', null, (new DateTimeImmutable($r['draw_at']))->format('Y-m-d'), true) ?></div>
            <button type="submit" class="small secondary">Duplikovat</button>
        </form>
        <?php endif; ?>
        <?php if ($isAdmin && $r['status'] !== 'drawn'): ?>
        <form method="POST" action="events/v1/lottery/<?= $id ?>/round/<?= (int) $r['id'] ?>/remove" onsubmit="return confirm('Smazat kolo?')">
            <button type="submit" class="small secondary">Smazat kolo</button></form>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php if ($isAdmin): ?>
    <div class="card new">
    <form method="POST" action="events/v1/lottery/<?= $id ?>/round" class="row">
        <div><label>Nové kolo</label><?= $dt('draw_at', null, $lastDate, true) ?></div>
        <label class="inline"><input type="checkbox" name="substitute" value="1">losuje se náhradník</label>
        <button type="submit">Přidat kolo</button>
    </form>
    </div>
    <?php endif; ?>
</div>

<div class="card">
    <h2>Ceny</h2>
    <p class="muted">Cena s kolem se losuje v tom kole, cena bez kola v kterémkoli kole podle pořadí (menší číslo dřív).</p>
    <?php foreach (array_merge($prizes, $isAdmin ? [null] : []) as $p): ?>
    <div class="card<?= $p ? '' : ' new' ?>">
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
    </div>
    <?php endforeach; ?>
</div>

<div class="card">
    <h2>Anketa</h2>
    <p class="muted">Návštěvník odpoví při zapojení v profilu; povinné otázky jsou podmínkou zapojení. Po prvních odpovědích lze otázku jen přejmenovat.
        Možnosti pište každou na nový řádek.</p>
    <?php foreach (array_merge($questions, $isAdmin ? [null] : []) as $q): $locked = $q && (int) $q['answered'] > 0; ?>
    <div class="card<?= $q ? '' : ' new' ?>">
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
    </div>
    <?php endforeach; ?>
</div>

<div class="card">
    <h2>Údaje losování</h2>
    <form method="POST" action="events/v1/lottery/<?= $id ?>">
        <label>Název</label><input type="text" name="name" value="<?= $h($lottery['name']) ?>" required <?= $dis ?>>
        <label>Popis (zobrazí se v profilu)</label><textarea name="description" rows="3" <?= $dis ?>><?= $h($lottery['description']) ?></textarea>
        <label>Odkaz na pravidla</label><input type="url" name="rules_url" value="<?= $h($lottery['rules_url']) ?>" placeholder="https://…" <?= $dis ?>>
        <p class="muted">Adresa stránky s pravidly losování. V profilu je odkazem u souhlasu s pravidly, na papírovém lístku se vytiskne u souhlasu.</p>
        <div class="row">
            <div><label>Zapojit se lze od</label><?= $dt('join_from', $lottery['join_from']) ?></div>
            <div><label>do</label><?= $dt('join_until', $lottery['join_until']) ?></div>
        </div>
        <p class="muted">Období, kdy se návštěvník může v profilu zapojit (bez času od 0:00, do 23:59; prázdné = bez omezení).
            Skenovat lístky a losovat lze i mimo ně, jen musí být losování otevřené.</p>
        <label>Stav</label><select name="status" <?= $dis ?>>
            <?php foreach (LotteryService::STATUSES as $k => $label): ?><option value="<?= $k ?>" <?= $lottery['status'] === $k ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?></select>
        <p class="muted">Zapojit se a skenovat lze jen v otevřeném losování.</p>
        <?php if ($isAdmin): ?><p><button type="submit">Uložit</button></p><?php endif; ?>
    </form>
</div>
