<?php
/**
 * R1 (#48): nabídka rezervací pro školy – nastavení, výběr přednášek, přehled po přednáškách (rezervováno / kapacita /
 * volno, školy, kontakty s telefonem), úprava a zrušení rezervací, export, mazání telefonů, anonymizace.
 *
 * @var array $offer
 * @var array $state SchoolReservationService::bookingState()
 * @var array $candidates eventy dne akce (lectureCandidates)
 * @var array $lectures přednášky nabídky s obsazeností
 * @var array<int, list<array>> $byEvent rezervace podle přednášky (i zrušené)
 * @var int $phones počet uložených telefonů
 * @var int $editId rezervace, jejíž formulář má být otevřený
 */
use Events\Service\Reservation\ReservationConfig;
use Events\Service\Reservation\ReservationValidator;
use Events\Service\Reservation\SchoolReservationService;

$h = fn($v) => htmlspecialchars((string) $v);
$fmt = fn($dt, $f = 'j. n. Y G:i') => SchoolReservationService::fmt($dt, $f);
$oid = (int) $offer['id'];
$config = ReservationConfig::fromConfiguration();
$dt = function (string $name, ?string $value, string $placeholder = '') use ($h): string {
    $d = $value ? substr($value, 0, 10) : '';
    $t = $value ? SchoolReservationService::fmt($value, 'G:i') : '';
    return '<div class="dt"><input type="date" name="' . $name . '_date" value="' . $h($d) . '">'
        . '<input type="text" class="time" name="' . $name . '_time" inputmode="numeric" placeholder="' . $h($placeholder) . '" value="' . $h($t) . '"></div>';
};
$fields = fn(array $vars) => (string) (new \Pes\View\ViewFactory())->phpTemplateView(__DIR__ . '/_fields.php', $vars)
    ->setRenderer(new \Pes\View\Renderer\PhpTemplateRenderer());
$totalReserved = array_sum(array_map(fn($l) => (int) $l['occupied'], $lectures));
?>
<div class="card">
    <h1><?= $h($offer['name']) ?></h1>
    <p>Den akce <b><?= $h($fmt($offer['event_date'], 'j. n. Y')) ?></b> · stav <b><?= $h(SchoolReservationService::OFFER_STATUSES[$offer['status']] ?? $offer['status']) ?></b> ·
        <?= $state['open'] ? '<span class="ok">formulář na webu přijímá rezervace</span>' : '<span class="bad">' . $h($state['message']) . '</span>' ?></p>
    <?php if ($config->hasPlaceholderTexts()): ?>
        <p class="msg warning"><b>Nutno nahradit:</b> texty souhlasů nebo odkaz na zásady v konfiguraci (<code>ConfigurationEvents::reservation()</code>)
            jsou zatím jen náznak. Před zveřejněním formuláře je nahraďte (issue #140).</p>
    <?php endif; ?>
    <p class="muted">Formulář pro pedagogy je statická stránka webu se šablonou <code>vp/rezervace-skol</code> (kód nabídky
        <code><?= $h($offer['code']) ?></code> – bez nastavení nabízí nejnovější otevřenou nabídku).</p>
</div>

<div class="card">
    <h2>Přehled po přednáškách</h2>
    <p>Rezervováno celkem <b><?= (int) $totalReserved ?></b> osob.
        <a class="btn small" href="events/v1/reservation/offer/<?= $oid ?>/csv">Export CSV</a>
        <span class="muted">(aktivní rezervace bez telefonů)</span></p>
    <?php if (!$lectures): ?><p class="muted">V nabídce zatím nejsou přednášky – vyberte je níže.</p><?php endif; ?>
    <?php foreach ($lectures as $l):
        $eid = (int) $l['id'];
        $cap = $l['capacity'] === null ? null : (int) $l['capacity'];
        $pct = $cap ? min(100, (int) round(100 * $l['occupied'] / $cap)) : 0;
        $list = $byEvent[$eid] ?? [];
        $activeList = array_filter($list, fn($r) => $r['status'] === SchoolReservationService::ACTIVE);
        ?>
        <div class="card" id="e<?= $eid ?>">
            <h2><?= $h($fmt($l['start'], 'G:i')) ?>–<?= $h($fmt($l['end'], 'G:i')) ?> <?= $h($l['title'] ?? '(bez názvu)') ?></h2>
            <p>Rezervováno <b><?= (int) $l['occupied'] ?></b> / kapacita <b><?= $cap ?? '–' ?></b> / volno
                <b class="<?= $l['free'] === 0 ? 'bad' : '' ?>"><?= $l['free'] ?? '–' ?></b> · skupin <?= count($activeList) ?>
                <?php if ($cap === null): ?><span class="bad">Bez kapacity – nelze rezervovat (nastavte ji ve správě eventů).</span><?php endif; ?>
                <?php if (!(int) $l['published']): ?><span class="bad">Nezveřejněný event – nelze rezervovat.</span><?php endif; ?>
                <?php if ($cap !== null && $l['occupied'] > $cap): ?><span class="bad">Kapacita překročena.</span><?php endif; ?></p>
            <?php if ($cap): ?><div class="bar <?= $l['occupied'] > $cap ? 'over' : '' ?>"><span style="width:<?= $pct ?>%"></span></div><?php endif; ?>
            <?php if ($list): ?>
            <table>
                <tr><th>Škola</th><th>Obor, třída</th><th>Osob</th><th>Pedagog</th><th></th></tr>
                <?php foreach ($list as $r): $rid = (int) $r['id']; $isActive = $r['status'] === SchoolReservationService::ACTIVE; ?>
                <tr id="r<?= $rid ?>" class="<?= $isActive ? '' : 'cancelled' ?>">
                    <td><b><?= $h($r['school_name']) ?></b>, <?= $h($r['school_place']) ?>
                        <?php if (!$isActive): ?><br><span class="bad">zrušeno <?= $h($fmt($r['cancelled_at'])) ?></span><?php endif; ?>
                        <?php if ($r['anonymized_at']): ?><br><span class="muted">anonymizováno</span><?php endif; ?>
                        <?php if ($r['note'] !== null): ?><br><span class="muted"><?= nl2br($h($r['note'])) ?></span><?php endif; ?></td>
                    <td><?= $h(ReservationValidator::FIELDS_SHORT[$r['field_code']] ?? $r['field_code']) ?><?= $r['school_class'] !== null ? ', ' . $h($r['school_class']) : '' ?></td>
                    <td class="nowrap"><?= (int) $r['pupils'] ?> + <?= (int) $r['teachers'] ?></td>
                    <td><?= $h($r['contact_name'] ?? '') ?><br><a href="mailto:<?= $h($r['contact_email']) ?>"><?= $h($r['contact_email']) ?></a>
                        <?php if ($r['contact_phone'] !== null): ?><br><a class="btn small" href="<?= $h(ReservationValidator::telHref($r['contact_phone'])) ?>">zavolat <?= $h($r['contact_phone']) ?></a>
                        <?php elseif ($r['phone_deleted_at'] !== null): ?><br><span class="muted">telefon smazán</span><?php endif; ?></td>
                    <td class="nowrap"><?php if ($isActive && !$r['anonymized_at']): ?><a href="events/v1/reservation/offer/<?= $oid ?>?edit=<?= $rid ?>#r<?= $rid ?>">Upravit</a><?php endif; ?></td>
                </tr>
                <?php if ($isActive && $editId === $rid): ?>
                <tr><td colspan="5">
                    <div class="card">
                        <form method="POST" action="events/v1/reservation/item/<?= $rid ?>">
                            <?= $fields(['mode' => 'admin', 'values' => $r, 'lectures' => $lectures, 'config' => $config, 'idPrefix' => 'a' . $rid]) ?>
                            <p class="muted">Správce smí kapacitu vědomě překročit (zobrazí se upozornění). Telefon mění jen pedagog.</p>
                            <label class="check"><input type="checkbox" name="notify" value="1" checked> poslat pedagogovi e-mail o změně</label>
                            <p><button type="submit">Uložit</button> <a href="events/v1/reservation/offer/<?= $oid ?>#e<?= $eid ?>">Zavřít</a></p>
                        </form>
                        <form method="POST" action="events/v1/reservation/item/<?= $rid ?>/cancel" onsubmit="return confirm('Zrušit rezervaci školy?')">
                            <label class="check"><input type="checkbox" name="notify" value="1" checked> poslat pedagogovi e-mail o zrušení</label>
                            <p><button type="submit" class="secondary small">Zrušit rezervaci</button></p>
                        </form>
                    </div>
                </td></tr>
                <?php endif; ?>
                <?php endforeach; ?>
            </table>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<div class="card">
    <h2>Přednášky v nabídce</h2>
    <p class="muted">Eventy dne akce z rozpisu programu. Zaškrtněte přednášky, na které se mohou školy přihlásit.
        Rezervovat lze jen zveřejněnou přednášku se zadanou kapacitou, která ještě nezačala. Kapacitu a zveřejnění nastavíte
        ve správě eventů (statická stránka <code>vp/event</code>).</p>
    <?php if (!$candidates): ?><p class="bad">Na den akce nejsou v rozpisu žádné eventy.</p><?php endif; ?>
    <form method="POST" action="events/v1/reservation/offer/<?= $oid ?>/events">
        <table>
            <?php foreach ($candidates as $c): ?>
            <tr>
                <td><label class="check"><input type="checkbox" name="events[]" value="<?= (int) $c['id'] ?>" <?= $c['in_offer'] ? 'checked' : '' ?>>
                    <?= $h($fmt($c['start'], 'G:i')) ?>–<?= $h($fmt($c['end'], 'G:i')) ?> <b><?= $h($c['title'] ?? '(bez obsahu)') ?></b></label></td>
                <td class="muted"><?= $h($c['type_name'] ?? '') ?></td>
                <td class="nowrap"><?= $c['capacity'] === null ? '<span class="bad">bez kapacity</span>' : 'kapacita ' . (int) $c['capacity'] ?></td>
                <td><?= (int) $c['published'] ? '' : '<span class="bad">nezveřejněno</span>' ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php if ($candidates): ?><p><button type="submit">Uložit výběr</button></p><?php endif; ?>
    </form>
</div>

<div class="card">
    <h2>Nastavení nabídky</h2>
    <form method="POST" action="events/v1/reservation/offer/<?= $oid ?>">
        <div class="grid2">
            <div><label>Název</label><input type="text" name="name" required value="<?= $h($offer['name']) ?>"></div>
            <div><label>Kód</label><input type="text" name="code" required pattern="[a-z0-9][a-z0-9\-]{1,48}" value="<?= $h($offer['code']) ?>"></div>
            <div><label>Den akce</label><input type="date" name="event_date" required value="<?= $h($offer['event_date']) ?>"></div>
            <div><label>Stav</label><select name="status">
                <?php foreach (SchoolReservationService::OFFER_STATUSES as $k => $v): ?>
                    <option value="<?= $h($k) ?>" <?= $offer['status'] === $k ? 'selected' : '' ?>><?= $h($v) ?></option>
                <?php endforeach; ?></select></div>
            <div><label>Rezervace od</label><?= $dt('booking_from', $offer['booking_from'], '0:00') ?></div>
            <div><label>Rezervace do</label><?= $dt('booking_until', $offer['booking_until'], '23:59') ?></div>
            <div><label>Uzávěrka změn pedagogem</label><?= $dt('change_until', $offer['change_until'], '23:59') ?></div>
        </div>
        <p class="muted">Prázdné „od / do“ = bez omezení (rezervace jdou, dokud je nabídka otevřená a přednáška nezačala).
            Prázdná uzávěrka = <?= (int) $config->changeDaysBefore ?> dny před akcí ve 23:59. Po uzávěrce mění rezervace jen správce.</p>
        <p><button type="submit">Uložit nabídku</button></p>
    </form>
</div>

<div class="card">
    <h2>Telefony a osobní údaje</h2>
    <p>Uložených telefonů: <b><?= (int) $phones ?></b>. Telefony se smažou automaticky po konci dne přednášky, hned při zrušení
        rezervace, nebo tlačítkem.</p>
    <form class="inline" method="POST" action="events/v1/reservation/offer/<?= $oid ?>/phones-delete" onsubmit="return confirm('Smazat všechny telefony pedagogů v této nabídce?')">
        <button type="submit" class="secondary small">Smazat telefony</button>
    </form>
    <form class="inline" method="POST" action="events/v1/reservation/offer/<?= $oid ?>/anonymize" onsubmit="return confirm('Anonymizovat všechny rezervace? Smažou se e-maily, jména a poznámky. Vrátit to nejde.')">
        <button type="submit" class="secondary small">Anonymizovat po akci</button>
    </form>
    <p class="muted">Anonymizace (až po dni akce) smaže e-maily, jména, poznámky a vazby na účty; škola, obor, třída a počty
        zůstanou pro statistiku.</p>
</div>
