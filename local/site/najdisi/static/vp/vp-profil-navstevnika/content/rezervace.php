<?php
/**
 * R1 (#48): blok „Rezervace školy“ v profilu – rezervace pedagoga přiřazené k účtu podle ověřeného e-mailu z registrace.
 * Vkládá template.php při zapnutém přepínači vp.rezervace. Očekává $container a $loginName. Telefon se tu nezobrazuje.
 */
use Events\Service\Reservation\SchoolReservationService;
use Events\Service\Reservation\ReservationValidator;
use Events\Service\Lottery\AuthLookup;

/** @var SchoolReservationService $reservationService */
$reservationService = $container->get(SchoolReservationService::class);
try {
    $reservationService->linkToLogin($loginName, $container->get(AuthLookup::class)->emailOf($loginName));
} catch (\Throwable $e) {
    // profil se zobrazí i bez párování
}
$schoolReservations = $reservationService->reservationsOfLogin($loginName);
$rfmt = fn($dt, $f = 'j. n. Y G:i') => SchoolReservationService::fmt($dt, $f);
?>
<?php if ($schoolReservations): ?>
<div class="ui segment vp-rezervace-profil">
    <h3 class="ui header">Rezervace školy</h3>
    <table class="ui very basic compact table">
        <?php foreach ($schoolReservations as $sr): ?>
        <tr<?= $sr['status'] !== SchoolReservationService::ACTIVE ? ' style="opacity:.6"' : '' ?>>
            <td><b><?= htmlspecialchars((string) $sr['event_title']) ?></b><br>
                <?= htmlspecialchars($rfmt($sr['event_start'])) ?>–<?= htmlspecialchars($rfmt($sr['event_end'], 'G:i')) ?></td>
            <td><?= htmlspecialchars($sr['school_name']) ?>, <?= htmlspecialchars($sr['school_place']) ?><br>
                <?= htmlspecialchars(ReservationValidator::FIELDS_SHORT[$sr['field_code']] ?? $sr['field_code']) ?><?= $sr['school_class'] !== null ? ', ' . htmlspecialchars($sr['school_class']) : '' ?></td>
            <td>žáci <?= (int) $sr['pupils'] ?> + pedagogové <?= (int) $sr['teachers'] ?></td>
            <td><?= $sr['status'] === SchoolReservationService::ACTIVE ? 'platná' : 'zrušená' ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <p>Změnu nebo zrušení provedete odkazem z potvrzovacího e-mailu (do uzávěrky změn), potom přes organizátora.
        Ve formuláři rezervací pro školy se vám údaje školy předvyplní.</p>
</div>
<?php endif; ?>
