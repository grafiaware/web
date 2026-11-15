<?php
/** D1: losování kola z telefonu – účastníci, tlačítko Losovat, výherci, převzetí, náhradník. */
/** @var array $lottery */
/** @var array $round */
/** @var array $rounds */
/** @var int $participants */
/** @var int $tickets */
/** @var array $prizes */
/** @var array $winners */
use Events\Service\Lottery\LotteryDrawService;
$t = fn($dt) => (new DateTimeImmutable($dt))->format('G:i');
$rid = (int) $round['id'];
$back = "events/v1/lottery/round/{$rid}";
$latest = null;
foreach ($winners as $w) { if ($w['status'] === LotteryDrawService::W_DRAWN) { $latest = $w; break; } }
?>
<div class="card">
    <h1><?= htmlspecialchars($lottery['name']) ?> – kolo <?= $t($round['draw_at']) ?></h1>
    <p class="row">
        <?php foreach ($rounds as $r): ?>
            <a class="btn small <?= (int) $r['id'] === $rid ? '' : 'secondary' ?>" href="events/v1/lottery/round/<?= (int) $r['id'] ?>"><?= $t($r['draw_at']) ?><?= $r['status'] === 'drawn' ? ' ✓' : '' ?></a>
        <?php endforeach; ?>
    </p>
    <p>Účastníků s platným losem: <b><?= $participants ?></b> (losů <?= $tickets ?>).
        <?= (int) $round['substitute'] === 1 ? '<b>V tomto kole se losuje náhradník.</b>' : 'Bez náhradníka.' ?></p>
</div>
<?php if ($latest): ?>
<div class="card" style="text-align:center">
    <p class="muted">Vylosován(a) – <?= htmlspecialchars($latest['prize_name']) ?></p>
    <div class="winner"><?= htmlspecialchars($latest['display_name']) ?></div>
</div>
<?php endif; ?>
<div class="card">
    <?php if (!$prizes): ?>
        <p>V tomto kole už není co losovat.</p>
    <?php else: ?>
        <form method="POST" action="events/v1/lottery/round/<?= $rid ?>/draw">
            <label for="prize">Cena</label>
            <select name="prize" id="prize">
                <?php foreach ($prizes as $p): ?>
                    <option value="<?= (int) $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (zbývá <?= (int) $p['remaining'] ?>)<?= $p['assigned'] ? ' – cena kola' : '' ?></option>
                <?php endforeach; ?>
            </select>
            <p><button type="submit" class="big" <?= $participants ? '' : 'disabled' ?>>Losovat</button></p>
            <p><button type="submit" name="all" value="1" class="secondary" <?= $participants ? '' : 'disabled' ?>
                onclick="return confirm('Vylosovat najednou všechny zbývající ceny kola?')">Vylosovat všechny ceny kola</button></p>
        </form>
    <?php endif; ?>
</div>
<div class="card">
    <h2>Výherci kola</h2>
    <?php if (!$winners): ?><p class="muted">Zatím nikdo.</p><?php endif; ?>
    <table>
    <?php foreach ($winners as $w): ?>
        <tr>
            <td><b><?= htmlspecialchars($w['display_name']) ?></b><br><span class="muted"><?= htmlspecialchars($w['prize_name']) ?> ·
                <?= htmlspecialchars(LotteryDrawService::W_STATUSES[$w['status']] ?? $w['status']) ?><?= $w['replaces_id_fk'] ? ' · náhradník' : '' ?></span></td>
            <td style="text-align:right">
                <?php if ($w['status'] === LotteryDrawService::W_DRAWN): ?>
                    <form method="POST" action="events/v1/lottery/winner/<?= (int) $w['id'] ?>/claimed" class="inline">
                        <input type="hidden" name="place" value="misto"><input type="hidden" name="return" value="<?= $back ?>">
                        <button type="submit" class="small">Převzal na místě</button>
                    </form>
                    <?php if ((int) $round['substitute'] === 1): ?>
                    <form method="POST" action="events/v1/lottery/winner/<?= (int) $w['id'] ?>/substitute" class="inline"
                          onsubmit="return confirm('Výhra propadne a vylosuje se náhradník. Pokračovat?')">
                        <button type="submit" class="small secondary">Nedostavil se – náhradník</button>
                    </form>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
</div>
