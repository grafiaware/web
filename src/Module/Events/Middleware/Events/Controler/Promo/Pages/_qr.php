<?php
/**
 * F3 (#47, docs/vp/zadani/F1.md): oddíl „QR kódy a plakát“ v detailu odkazu (media manager) – QR webu PNG,
 * QR z QRdirectoru nahrané jako SVG (vyčištěné, s adresou přečtenou v prohlížeči), podklad plakátu PDF A4 / A3.
 *
 * @var array $link
 * @var array $svgs řádky promo_qr_svg
 * @var string $linkUrl veřejná adresa odkazu bez kanálu
 * @var array $channels kód => [label, group]
 * @var string $publicBase veřejná adresa webu
 */
use Events\Service\Promo\PosterDataService;
use Events\Service\Promo\PromoLinkService;

$h = fn($v) => htmlspecialchars((string) $v);
$id = (int) $link['id'];
$print = array_filter($channels, fn($c) => $c['group'] === 'print');
$other = array_diff_key($channels, $print);
?>
<div class="card" id="qr">
    <h2>QR kódy a plakát</h2>
    <p class="muted">QR pro tisk (billboard, leták, plakát) vytvořte v <b>QRdirectoru</b> s cílem <code><?= $h($linkUrl) ?>/&lt;kanál&gt;</code>
        (např. <code>/billboard</code>) a SVG nahrajte sem – použije se v podkladu plakátu. Cíl odkazu pak měníte už jen tady.</p>

    <?php if ($svgs): ?>
    <table>
        <tr><th>Náhled</th><th>Popisek a kanál</th><th>Kam QR vede (přečteno v prohlížeči)</th><th></th></tr>
        <?php foreach ($svgs as $s): $kind = PosterDataService::decodedKind($s['decoded_url'], $publicBase, (string) $link['code']); ?>
        <tr>
            <td><img src="data:image/svg+xml;base64,<?= base64_encode((string) $s['svg']) ?>" alt="QR <?= $h($s['label']) ?>"
                     style="width:90px;height:90px;object-fit:contain;background:#fff;border:1px solid #ddd"></td>
            <td><?= $h($s['label']) ?><br><span class="muted">kanál <?= $h($s['channel']) ?> · <?= $h(date('j. n. Y', strtotime((string) $s['created']))) ?> <?= $h($s['created_by'] ?? '') ?></span></td>
            <td style="word-break:break-all">
                <?php if ($kind === 'unknown'): ?><span class="bad">nepřečteno – ověřte skenem telefonem</span>
                <?php else: ?><?= $h($s['decoded_url']) ?><br>
                    <?= $kind === 'link' ? '<span class="ok">přímo tento odkaz</span>' : '<span class="muted">jiná adresa (QRdirector) – cestu ověřte skenem telefonem</span>' ?>
                <?php endif; ?></td>
            <td><form method="POST" action="events/v1/promo/<?= $id ?>/qr-svg/<?= (int) $s['id'] ?>/delete" onsubmit="return confirm('Smazat QR kód <?= $h(addslashes((string) $s['label'])) ?>?')">
                <button type="submit" class="small secondary">Smazat</button></form></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <p class="muted">Zatím žádný QR z QRdirectoru.</p>
    <?php endif; ?>

    <form method="POST" action="events/v1/promo/<?= $id ?>/qr-svg" class="card new" data-svg-upload data-link-url="<?= $h($linkUrl) ?>">
        <h2 style="font-size:16px">Nahrát QR z QRdirectoru (SVG)</h2>
        <div class="row">
            <div><label for="qr-file">Soubor SVG</label><input type="file" id="qr-file" accept=".svg,image/svg+xml" data-svg-file></div>
            <div><label for="qr-channel">Kanál</label><select id="qr-channel" name="channel" required>
                <option value="">vyberte</option>
                <optgroup label="Tisk"><?php foreach ($print as $k => $c): ?><option value="<?= $h($k) ?>"><?= $h($c['label']) ?> (<?= $h($k) ?>)</option><?php endforeach; ?></optgroup>
                <optgroup label="Ostatní"><?php foreach ($other as $k => $c): ?><option value="<?= $h($k) ?>"><?= $h($c['label']) ?> (<?= $h($k) ?>)</option><?php endforeach; ?></optgroup>
            </select></div>
            <div><label for="qr-label">Popisek (nepovinné)</label><input type="text" id="qr-label" name="label" maxlength="100" placeholder="např. billboard D1 Brno"></div>
        </div>
        <input type="hidden" name="svg_text"><input type="hidden" name="decoded_url">
        <div style="display:flex;gap:12px;align-items:center;margin:8px 0"><span data-svg-preview></span><span data-svg-info class="muted"></span></div>
        <p class="muted">SVG se před uložením vyčistí – zůstanou jen tvary QR kódu (žádné skripty, odkazy ani obrázky).</p>
        <p><button type="submit">Nahrát</button></p>
    </form>

    <h2 style="margin-top:14px">QR webu a podklad plakátu</h2>
    <p><a class="btn small secondary" href="events/v1/promo/<?= $id ?>/qr.png">QR webu (PNG)</a>
        <span class="muted">adresa s kanálem <code>qr</code> – pro obrazovky a web, ne pro tisk</span></p>
    <div data-poster data-font-regular="public/assets/fonts/liberation-sans/LiberationSans-Regular.ttf"
         data-font-bold="public/assets/fonts/liberation-sans/LiberationSans-Bold.ttf">
        <p><?php foreach (PosterDataService::FORMATS as $fmt): ?>
            <button type="button" class="small" data-poster-url="events/v1/promo/<?= $id ?>/poster" data-poster-format="<?= $h($fmt) ?>">Podklad plakátu PDF <?= $h($fmt) ?></button>
        <?php endforeach; ?></p>
        <p class="muted">Bez designu – QR kódy (web s kanálem <code><?= $h(PosterDataService::POSTER_CHANNEL) ?></code> a nahrané z QRdirectoru)
            a texty jako samostatné objekty pro grafika (Illustrator, InDesign…). Text plakátu upravíte níže v „Upravit odkaz“.</p>
        <p data-poster-status class="muted"></p>
    </div>
</div>
<script src="public/assets/lib/jsQR-1.4.0.min.js"></script>
<script src="public/assets/lib/jspdf-4.2.1.umd.min.js"></script>
<script src="public/assets/lib/svg2pdf-2.9.0.umd.min.js"></script>
<script src="public/assets/lib/qrcode-generator-2.0.4.js"></script>
<script src="public/site/common/js/promo-qr.js"></script>
<script src="public/site/common/js/promo-poster-pdf.js"></script>
<script>
    document.querySelectorAll('[data-svg-upload]').forEach(PromoQr.bindUpload);
    document.querySelectorAll('[data-poster]').forEach(PromoPosterPdf.bind);
</script>
