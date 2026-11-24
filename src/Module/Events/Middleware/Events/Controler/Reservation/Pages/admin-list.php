<?php
/** R1 (#48): přehled nabídek rezervací pro školy. */
/** @var array $offers */
/** @var int $year navrhovaný ročník nové nabídky */
use Events\Service\Reservation\SchoolReservationService;

$h = fn($v) => htmlspecialchars((string) $v);
?>
<div class="card">
    <h1>Rezervace pro školy</h1>
    <?php if (!$offers): ?><p class="muted">Zatím žádná nabídka. Jedna nabídka = jeden ročník veletrhu.</p><?php endif; ?>
    <table>
        <?php foreach ($offers as $o): ?>
        <tr>
            <td><a href="events/v1/reservation/offer/<?= (int) $o['id'] ?>"><b><?= $h($o['name']) ?></b></a>
                <span class="muted"><?= $h($o['code']) ?></span></td>
            <td class="nowrap"><?= $h(SchoolReservationService::fmt($o['event_date'], 'j. n. Y')) ?></td>
            <td><?= $h(SchoolReservationService::OFFER_STATUSES[$o['status']] ?? $o['status']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<div class="card">
    <h2>Nová nabídka</h2>
    <form method="POST" action="events/v1/reservation/offer">
        <div class="grid2">
            <div><label for="name">Název</label><input type="text" id="name" name="name" required value="Přednášky pro školy <?= $year ?>"></div>
            <div><label for="code">Kód (v adrese formuláře)</label><input type="text" id="code" name="code" required pattern="[a-z0-9][a-z0-9\-]{1,48}" value="skoly-<?= $year ?>"></div>
            <div><label for="event_date">Den akce</label><input type="date" id="event_date" name="event_date" required></div>
        </div>
        <input type="hidden" name="status" value="<?= SchoolReservationService::OFFER_DRAFT ?>">
        <p class="muted">Nabídka vznikne jako „připravuje se“ – formulář na webu ji nenabídne, dokud ji neotevřete.</p>
        <p><button type="submit">Založit</button></p>
    </form>
</div>
