<?php
/**
 * F3 (#47, docs/vp/zadani/F1.md): ověření QR kódů (media manager) – všechny druhy QR, které web vydává nebo má nahrané,
 * s čitelnou adresou a kontrolou, že je https:// a veřejná adresa webu. Vzorové kódy nic nezapíšou.
 * QR webu se vykreslí v prohlížeči z adresy (qrcode-generator); u QRdirectoru se ukáže uložená adresa – jeho
 * přesměrování na náš odkaz ověří až sken telefonem.
 *
 * @var array $items [group, label, url, source (web|svg|legacy), public, kind?, linkId?]
 * @var string $publicBase
 */
$h = fn($v) => htmlspecialchars((string) $v);
$groups = [];
foreach ($items as $it) {
    $groups[$it['group']][] = $it;
}
?>
<div class="card">
    <h1>Ověření QR kódů</h1>
    <p>Veřejná adresa webu: <code><?= $h($publicBase) ?></code>. Každý QR naskenujte i telefonem – musí otevřít uvedenou adresu
        (u QRdirectoru přes jeho přesměrování).</p>
    <?php if (!str_starts_with($publicBase, 'https://')): ?>
        <p class="msg warning">Web neběží na https:// – QR kódy pro tisk a skener losování na produkci musí vést na https.</p>
    <?php endif; ?>
</div>

<?php foreach ($groups as $group => $rows): ?>
<div class="card">
    <h2><?= $h($group) ?></h2>
    <table>
        <tr><th style="width:110px">QR</th><th>Co</th><th>Zakódovaná adresa</th><th>Kontrola</th></tr>
        <?php foreach ($rows as $it): ?>
        <tr>
            <td><?php if ($it['source'] === 'web' || $it['source'] === 'legacy'): ?>
                    <span data-qr-text="<?= $h($it['url']) ?>" style="display:block;width:100px;height:100px"></span>
                <?php else: ?><span class="muted">SVG u odkazu</span><?php endif; ?></td>
            <td><?= $h($it['label']) ?>
                <?php if (!empty($it['linkId'])): ?><br><a href="events/v1/promo/<?= (int) $it['linkId'] ?><?= $it['source'] === 'svg' ? '#qr' : '' ?>" class="muted">detail odkazu</a><?php endif; ?></td>
            <td style="word-break:break-all"><?= $it['url'] ? $h($it['url']) : '<span class="bad">nepřečteno</span>' ?></td>
            <td><?php if ($it['source'] === 'svg'): ?>
                    <?= match ($it['kind'] ?? 'unknown') {
                        'link' => '<span class="ok">přímo náš odkaz</span>',
                        'external' => '<span class="muted">adresa QRdirectoru – ověřte skenem, že vede na náš odkaz</span>',
                        default => '<span class="bad">QR nepřečten – ověřte skenem</span>',
                    } ?>
                <?php elseif ($it['public']): ?><span class="ok">https, veřejná adresa webu</span>
                <?php elseif ($it['source'] === 'legacy'): ?><span class="muted">jiná adresa (tištěné materiály 2026) – ověřte, že stále funguje</span>
                <?php else: ?><span class="bad">není https / není veřejná adresa webu</span><?php endif; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endforeach; ?>
<script src="public/assets/lib/qrcode-generator-2.0.4.js"></script>
<script src="public/site/common/js/promo-qr.js"></script>
<script>PromoQr.renderQr(document);</script>
