<?php
/**
 * F2 (#47): detail odkazu – adresa, cíl, úprava, podklady ke zkopírování, návštěvy a registrace po kanálech a dnech,
 * historie cíle.
 *
 * @var array $link
 * @var array $filter
 * @var array $row řádek PromoLinkService::overview()['links']
 * @var bool $authError
 * @var array $daily den => [kanál => počet]
 * @var array $history
 * @var int $visitCount
 * @var array $copyRows
 * @var string $targetUrl
 * @var \Events\Service\Promo\PromoLinkService $links
 * @var array $companies
 * @var \Events\Service\Promo\PromoConfig $config
 * @var bool $qrEnabled F3 – přepínač vp.qr-plakaty
 * @var array $svgs F3 – QR z QRdirectoru
 * @var string $linkUrl F3 – veřejná adresa odkazu bez kanálu
 * @var array $channels kanály
 * @var string $publicBase veřejná adresa webu
 */
use Events\Service\Promo\PromoLinkService;

$h = fn($v) => htmlspecialchars((string) $v);
$fmt = fn($dt) => $dt ? date('j. n. Y G:i', strtotime((string) $dt)) : '';
$q = http_build_query(array_filter(['from' => $filter['from'], 'to' => $filter['to']], fn($v) => $v !== null && $v !== ''));
$view = fn(string $file, array $vars) => (new \Pes\View\ViewFactory())->phpTemplateView(__DIR__ . '/' . $file, $vars)
    ->setRenderer(new \Pes\View\Renderer\PhpTemplateRenderer());
$channelsUsed = [];
foreach ($daily as $perChannel) {
    $channelsUsed += $perChannel;
}
?>
<div class="card">
    <h1><?= $h($link['name']) ?> <?= (int) $link['active'] ? '' : '<span class="bad">(vypnutý)</span>' ?></h1>
    <table>
        <tr><th>Adresa</th><td><code><?= $h('events/v1/r/' . $link['code']) ?></code> + kanál, např. <code><?= $h('events/v1/r/' . $link['code'] . '/fb') ?></code></td></tr>
        <tr><th>Od koho</th><td><?= $h(PromoLinkService::OWNER_TYPES[$link['owner_type']] ?? $link['owner_type']) ?><?= $link['company_name'] ? ' – ' . $h($link['company_name']) : '' ?></td></tr>
        <tr><th>Cíl</th><td><?= $h($links->targetLabel($link)) ?><br>
            <?php if ((int) $link['active']): ?><a href="<?= $h($targetUrl) ?>" target="_blank" rel="noopener" class="muted"><?= $h($targetUrl) ?></a><?php endif; ?></td></tr>
        <?php if ($link['note']): ?><tr><th>Poznámka</th><td><?= nl2br($h($link['note'])) ?></td></tr><?php endif; ?>
        <tr><th>Založil</th><td><?= $h($fmt($link['created'])) ?> <?= $h($link['created_by'] ?? '') ?></td></tr>
    </table>
</div>

<div class="card">
    <h2>Návštěvy a registrace</h2>
    <form method="GET" action="events/v1/promo/<?= (int) $link['id'] ?>" class="row">
        <div><label for="d-from">Od</label><input type="date" id="d-from" name="from" value="<?= $h($filter['from']) ?>"></div>
        <div><label for="d-to">Do</label><input type="date" id="d-to" name="to" value="<?= $h($filter['to']) ?>"></div>
        <div style="flex:0 0 auto"><button type="submit" class="small">Filtrovat</button></div>
    </form>
    <?php if ($authError): ?><p class="msg warning">Registrace podle zdroje se nepodařilo načíst (DB auth).</p><?php endif; ?>
    <table>
        <tr><th>Kanál</th><th class="num">Návštěvy</th><th class="num">Registrace</th><th class="num">Potvrzené</th></tr>
        <?php foreach ($row['channels'] as $ch => $n): ?>
            <tr><td><?= $h($links->channelLabel((string) $ch)) ?> <span class="muted"><?= $h($ch) ?></span></td>
                <td class="num"><?= (int) $n['visits'] ?></td><td class="num"><?= (int) $n['registered'] ?></td><td class="num"><?= (int) $n['confirmed'] ?></td></tr>
        <?php endforeach; ?>
        <tr class="sum"><td>Celkem</td><td class="num"><?= (int) $row['visits'] ?></td><td class="num"><?= (int) $row['registered'] ?></td><td class="num"><?= (int) $row['confirmed'] ?></td></tr>
    </table>
    <?php if ($daily): ?>
    <details style="margin-top:10px"><summary>Návštěvy po dnech</summary>
        <table>
            <tr><th>Den</th><?php foreach (array_keys($channelsUsed) as $ch): ?><th class="num"><?= $h($ch) ?></th><?php endforeach; ?><th class="num">Celkem</th></tr>
            <?php foreach ($daily as $day => $perChannel): ?>
                <tr><td class="nowrap"><?= $h(date('j. n. Y', strtotime($day))) ?></td>
                    <?php foreach (array_keys($channelsUsed) as $ch): ?><td class="num"><?= (int) ($perChannel[$ch] ?? 0) ?></td><?php endforeach; ?>
                    <td class="num"><?= array_sum($perChannel) ?></td></tr>
            <?php endforeach; ?>
        </table>
    </details>
    <?php endif; ?>
</div>

<div class="card">
    <h2>Podklady ke zkopírování</h2>
    <p class="muted">Pro každý kanál vlastní adresa – návštěvy se pak počítají podle kanálu. Do tisku a QRdirectoru dejte adresu
        tiskového kanálu (např. <code>/plakat</code>); cíl pak měníte už jen tady.</p>
    <?= $view('_copy.php', ['rows' => $copyRows, 'idPrefix' => 'c']) ?>
</div>

<?php if ($qrEnabled): ?>
<?= $view('_qr.php', ['link' => $link, 'svgs' => $svgs, 'linkUrl' => $linkUrl, 'channels' => $channels, 'publicBase' => $publicBase]) ?>
<?php endif; ?>

<div class="card">
    <h2>Upravit odkaz</h2>
    <form method="POST" action="events/v1/promo/<?= (int) $link['id'] ?>">
        <?= $view('_link-form.php', ['link' => $link, 'codeLocked' => $visitCount > 0, 'companies' => $companies, 'config' => $config, 'idPrefix' => 'e']) ?>
        <p><button type="submit">Uložit</button></p>
    </form>
</div>

<div class="card">
    <h2>Historie cíle</h2>
    <table>
        <tr><th>Kdy</th><th>Kdo</th><th>Cíl</th></tr>
        <?php foreach ($history as $hrow): ?>
            <tr><td class="nowrap"><?= $h($fmt($hrow['changed'])) ?></td><td><?= $h($hrow['changed_by'] ?? '') ?></td>
                <td><?= $h($links->targetLabel(['target' => $hrow['target'], 'target_path' => $hrow['target_path'], 'active' => 1])) ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>
