<?php
/**
 * F2 (#47): seznam odkazů s přehledem návštěv a registrací, filtr období a vlastníka, nový odkaz.
 *
 * @var array $filter
 * @var array $overview PromoLinkService::overview() + authError
 * @var \Events\Service\Promo\PromoLinkService $links
 * @var array $companies
 * @var \Events\Service\Promo\PromoConfig $config
 */
use Events\Service\Promo\PromoLinkService;

$h = fn($v) => htmlspecialchars((string) $v);
$q = http_build_query(array_filter($filter, fn($v) => $v !== null && $v !== ''));
?>
<div class="card">
    <h1>Odkazy a zdroje registrací</h1>
    <p class="muted">Trvalé odkazy <code>/events/v1/r/&lt;kód&gt;/&lt;kanál&gt;</code>: adresa se nemění, cíl ano. Návštěvy jsou součty
        za den a kanál (bez údajů o návštěvnících), registrace se počítají podle zdroje uloženého při registraci.</p>
    <form method="GET" action="events/v1/promo" class="row">
        <div><label for="f-from">Od</label><input type="date" id="f-from" name="from" value="<?= $h($filter['from']) ?>"></div>
        <div><label for="f-to">Do</label><input type="date" id="f-to" name="to" value="<?= $h($filter['to']) ?>"></div>
        <div><label for="f-owner">Od koho</label>
            <select id="f-owner" name="owner"><option value="">všichni</option>
                <?php foreach (PromoLinkService::OWNER_TYPES as $k => $label): ?>
                    <option value="<?= $h($k) ?>" <?= $filter['owner'] === $k ? 'selected' : '' ?>><?= $h($label) ?></option>
                <?php endforeach; ?>
            </select></div>
        <div style="flex:0 0 auto"><button type="submit" class="small">Filtrovat</button>
            <a class="btn small secondary" href="events/v1/promo-csv<?= $q !== '' ? '?' . $h($q) : '' ?>">CSV</a></div>
    </form>
    <?php if ($overview['authError']): ?>
        <p class="msg warning">Registrace podle zdroje se nepodařilo načíst (DB auth). Návštěvy jsou úplné.</p>
    <?php endif; ?>
</div>

<div class="card">
    <table>
        <tr><th>Kód</th><th>Název</th><th>Od koho</th><th class="num">Návštěvy</th><th class="num">Registrace</th><th class="num">Potvrzené</th></tr>
        <?php foreach ($overview['links'] as $row): $l = $row['link']; ?>
        <tr class="<?= (int) $l['active'] ? '' : 'off' ?>">
            <td><a href="events/v1/promo/<?= (int) $l['id'] ?><?= $q !== '' ? '?' . $h($q) : '' ?>"><code><?= $h($l['code']) ?></code></a><?= (int) $l['active'] ? '' : ' <span class="muted">(vypnutý)</span>' ?></td>
            <td><?= $h($l['name']) ?><?= $l['company_name'] ? '<br><span class="muted">' . $h($l['company_name']) . '</span>' : '' ?></td>
            <td><?= $h(PromoLinkService::OWNER_TYPES[$l['owner_type']] ?? $l['owner_type']) ?></td>
            <td class="num"><?= (int) $row['visits'] ?></td><td class="num"><?= (int) $row['registered'] ?></td><td class="num"><?= (int) $row['confirmed'] ?></td>
        </tr>
        <?php endforeach; ?>
        <?php foreach ($overview['utm'] as $u): ?>
        <tr><td><span class="muted">UTM</span></td><td colspan="2"><?= $h($u['label']) ?></td><td class="num">–</td>
            <td class="num"><?= (int) $u['registered'] ?></td><td class="num"><?= (int) $u['confirmed'] ?></td></tr>
        <?php endforeach; ?>
        <tr class="sum"><td colspan="3">Celkem</td><td class="num"><?= (int) $overview['totals']['visits'] ?></td>
            <td class="num"><?= (int) $overview['totals']['registered'] ?></td><td class="num"><?= (int) $overview['totals']['confirmed'] ?></td></tr>
    </table>
    <?php if (!$overview['links']): ?><p class="muted">Zatím žádný odkaz.</p><?php endif; ?>
</div>

<div class="card" id="novy">
    <h2>Nový odkaz</h2>
    <form method="POST" action="events/v1/promo">
        <?= (new \Pes\View\ViewFactory())->phpTemplateView(__DIR__ . '/_link-form.php',
            ['link' => null, 'codeLocked' => false, 'companies' => $companies, 'config' => $config, 'idPrefix' => 'n'])
            ->setRenderer(new \Pes\View\Renderer\PhpTemplateRenderer()) ?>
        <p><button type="submit">Založit odkaz</button></p>
    </form>
</div>
