<?php
/**
 * R1 (#48): správa rezervace pedagogem (odkaz z e-mailu). Do uzávěrky změna a zrušení, potom jen souhrn.
 *
 * @var array $r rezervace (SchoolReservationService::reservationByToken())
 * @var string $token
 * @var bool $canChange
 * @var string $deadline
 * @var array $lectures
 * @var \Events\Service\Reservation\ReservationConfig $config
 * @var bool $isNew právě vytvořená rezervace
 * @var bool $loggedIn
 */
use Events\Service\Reservation\ReservationValidator;
use Events\Service\Reservation\SchoolReservationService;

$h = fn($v) => htmlspecialchars((string) $v);
$fmt = fn($dt, $f = 'j. n. Y G:i') => SchoolReservationService::fmt($dt, $f);
$active = $r['status'] === SchoolReservationService::ACTIVE;
$action = 'events/v1/reservation/manage/' . $h($token);
?>
<script>try { sessionStorage.removeItem('vp-rezervace-skol'); } catch (e) {}</script>
<div class="card">
    <h1><?= $h($r['offer_name']) ?></h1>
    <?php if (!$active): ?>
        <p class="msg warning"><b>Rezervace je zrušená</b> (<?= $h($fmt($r['cancelled_at'])) ?><?= $r['cancelled_by'] !== SchoolReservationService::CANCELLED_SELF ? ', zrušil organizátor' : '' ?>).</p>
    <?php endif; ?>
    <table>
        <tr><th>Přednáška</th><td><b><?= $h($r['event_title']) ?></b><br><?= $h($fmt($r['event_start'])) ?>–<?= $h($fmt($r['event_end'], 'G:i')) ?></td></tr>
        <tr><th>Škola</th><td><?= $h($r['school_name']) ?>, <?= $h($r['school_place']) ?></td></tr>
        <tr><th>Cílový obor</th><td><?= $h(ReservationValidator::FIELDS[$r['field_code']] ?? $r['field_code']) ?></td></tr>
        <?php if ($r['school_class'] !== null): ?><tr><th>Třída</th><td><?= $h($r['school_class']) ?></td></tr><?php endif; ?>
        <tr><th>Počet osob</th><td>žáci <?= (int) $r['pupils'] ?> + pedagogové <?= (int) $r['teachers'] ?> = <b><?= (int) $r['pupils'] + (int) $r['teachers'] ?></b></td></tr>
        <tr><th>Kontakt</th><td><?= $h($r['contact_name'] ?? '') ?> <?= $h($r['contact_email']) ?></td></tr>
        <tr><th>Telefon</th><td><?= $r['contact_phone'] !== null ? $h($r['contact_phone']) . ' <span class="muted">(jen pro ověření příchodu skupiny, po akci se smaže)</span>'
            : '<span class="muted">nezadán</span>' ?></td></tr>
        <?php if ($r['note'] !== null): ?><tr><th>Poznámka</th><td><?= nl2br($h($r['note'])) ?></td></tr><?php endif; ?>
    </table>
    <?php if ($active): ?>
        <p class="muted">Přijďte prosím včas – rezervovaná místa držíme do začátku přednášky.
            <?= $canChange ? 'Změny a zrušení jsou možné do ' . $h($fmt($deadline)) . '.' : '' ?></p>
    <?php endif; ?>
    <p class="muted">Tuto stránku otevřete odkazem z potvrzovacího e-mailu. Odkaz nikomu nepřeposílejte.</p>
</div>

<?php if ($active && !$loggedIn): ?>
<div class="card">
    <h2>Zaregistrujte se – rezervace uvidíte v profilu</h2>
    <p>Na <a href="">webu veletrhu</a> se zaregistrujte se stejným e-mailem (<?= $h($r['contact_email']) ?>). Po přihlášení uvidíte
        své rezervace v profilu a příští rezervace se vám předvyplní. Registrace není nutná – rezervace platí i bez ní.</p>
</div>
<?php endif; ?>

<?php if ($active && $canChange): ?>
<div class="card">
    <h2>Změnit rezervaci</h2>
    <p class="muted">Můžete změnit počty a údaje nebo vybrat jinou přednášku s volnými místy.</p>
    <form method="POST" action="<?= $action ?>">
        <?= (new \Pes\View\ViewFactory())->phpTemplateView(__DIR__ . '/_fields.php',
            ['mode' => 'owner', 'values' => $r, 'lectures' => $lectures, 'config' => $config, 'idPrefix' => 'm'])
            ->setRenderer(new \Pes\View\Renderer\PhpTemplateRenderer()) ?>
        <p><button type="submit">Uložit změny</button></p>
    </form>
</div>
<div class="card">
    <h2>Zrušit rezervaci</h2>
    <p>Když skupina nepřijde, zrušte prosím rezervaci – místa uvolníte dalším školám.</p>
    <form method="POST" action="<?= $action ?>/cancel" onsubmit="return confirm('Opravdu zrušit rezervaci? Vrátit to nejde, místa se uvolní.')">
        <button type="submit" class="secondary">Zrušit rezervaci</button>
    </form>
</div>
<?php elseif ($active): ?>
<div class="card">
    <p><b>Uzávěrka změn uplynula</b> (<?= $h($fmt($deadline)) ?>). Změnu nebo zrušení domluvte s organizátorem<?= $config->organizerContact !== '' ? ': ' . $h($config->organizerContact) : '' ?>.</p>
</div>
<?php endif; ?>
