<?php
/**
 * A3 (docs/vp/zadani/A3.md): nepotvrzené registrace – čekající zástupci firem a úklid.
 *
 * @var array $pendingRepresentatives [['loginName', 'company', 'email', 'created', 'daysLeft'], …]
 * @var array $preview ['visitors', 'representatives', 'total']
 * @var int $days
 * @var bool $canClean jen supervisor
 */
?>
<div class="ui segment nepotvrzene-registrace">
    <h3>Nepotvrzené registrace</h3>
    <p>Registrace, které nikdo nedokončil, se po <?= (int) $days ?> dnech od registrace smažou (účet i registrace).
       Zástupce firmy dokončete včas.</p>

    <h4>Čekající zástupci firem (<?= count($pendingRepresentatives) ?>)</h4>
    <?php if ($pendingRepresentatives): ?>
    <table class="ui compact celled table">
        <thead>
            <tr><th>Účet</th><th>Firma</th><th>E-mail</th><th>Registrace</th><th>Smaže se za</th></tr>
        </thead>
        <tbody>
            <?php foreach ($pendingRepresentatives as $r): ?>
            <tr<?= $r['daysLeft'] === 0 ? ' class="negative"' : ($r['daysLeft'] <= 3 ? ' class="warning"' : '') ?>>
                <td><?= htmlspecialchars($r['loginName']) ?></td>
                <td><?= htmlspecialchars($r['company']) ?></td>
                <td><?= htmlspecialchars($r['email']) ?></td>
                <td><?= htmlspecialchars(date('j. n. Y', strtotime($r['created']))) ?></td>
                <td><?= $r['daysLeft'] === 0 ? 'při nejbližším úklidu' : ($r['daysLeft'] . ' ' . ($r['daysLeft'] === 1 ? 'den' : ($r['daysLeft'] <= 4 ? 'dny' : 'dní'))) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p>Žádný zástupce firmy nečeká na dokončení registrace.</p>
    <?php endif; ?>

    <h4>Úklid</h4>
    <p>Starší než <?= (int) $days ?> dní: <b><?= (int) $preview['total'] ?></b>
       (návštěvníci <?= (int) $preview['visitors'] ?>, zástupci firem <?= (int) $preview['representatives'] ?>).</p>
    <?php if ($canClean && $preview['total'] > 0): ?>
    <form class="ui form" method="POST" action="auth/v1/admin/unconfirmed/clean"
          onsubmit="return confirm('Smazat <?= (int) $preview['total'] ?> nepotvrzených registrací? Smazání nejde vrátit.');">
        <button class="ui red button" type="submit">Smazat starší než <?= (int) $days ?> dní</button>
    </form>
    <?php elseif (!$canClean): ?>
    <p class="maly text">Úklid spouští supervisor.</p>
    <?php endif; ?>
</div>
