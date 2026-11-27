<?php
/**
 * F2 (#47): podklady ke zkopírování (ctrl+c / ctrl+v) po kanálech – adresa odkazu s kanálem, text příspěvku / e-mailu,
 * HTML odkaz pro web. Používá stránka odkazu (správa) i promo kit v profilu vystavovatele. Tlačítka obsluhuje skript
 * podle data-copy (id pole).
 *
 * @var array $rows PromoLinkService::copyRows()
 * @var string $idPrefix
 */
use Events\Service\Promo\PromoLinkService;

$h = fn($v) => htmlspecialchars((string) $v);
$p = $idPrefix ?? 'cp';
$groups = [];
foreach ($rows as $row) {
    $groups[$row['group']][] = $row;
}
?>
<?php foreach (PromoLinkService::GROUPS as $g => $gLabel): if (empty($groups[$g])) { continue; } ?>
<details class="vp-copy-group" <?= in_array($g, ['social', 'web'], true) ? 'open' : '' ?>>
    <summary><b><?= $h($gLabel) ?></b></summary>
    <?php foreach ($groups[$g] as $i => $row): $id = $p . '-' . $row['channel']; ?>
    <div class="vp-copy-row" style="margin:8px 0 12px">
        <div><b><?= $h($row['label']) ?></b> <span class="muted">kanál <code><?= $h($row['channel']) ?></code></span></div>
        <div class="copy"><input type="text" readonly id="<?= $h($id) ?>-url" value="<?= $h($row['url']) ?>">
            <button type="button" class="small" data-copy="<?= $h($id) ?>-url">Kopírovat adresu</button></div>
        <?php if ($row['text'] !== null): ?>
        <div class="copy"><textarea readonly rows="<?= $row['group'] === 'mail' ? 6 : 3 ?>" id="<?= $h($id) ?>-text"><?= $h($row['text']) ?></textarea>
            <button type="button" class="small" data-copy="<?= $h($id) ?>-text">Kopírovat text</button></div>
        <?php endif; ?>
        <?php if ($row['html'] !== null): ?>
        <div class="copy"><input type="text" readonly id="<?= $h($id) ?>-html" value="<?= $h($row['html']) ?>">
            <button type="button" class="small" data-copy="<?= $h($id) ?>-html">Kopírovat HTML</button></div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</details>
<?php endforeach; ?>
