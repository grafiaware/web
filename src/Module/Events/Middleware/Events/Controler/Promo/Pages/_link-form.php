<?php
/**
 * F2 (#47): pole formuláře odkazu (nový i úprava). Obal <form> dodá volající.
 *
 * @var array|null $link
 * @var bool $codeLocked kód už nejde změnit (odkaz má návštěvy)
 * @var array $companies
 * @var \Events\Service\Promo\PromoConfig $config
 * @var string $idPrefix
 */
use Events\Service\Promo\PromoConfig;
use Events\Service\Promo\PromoLinkService;

$h = fn($v) => htmlspecialchars((string) $v);
$p = $idPrefix ?? 'l';
$v = fn(string $k, $d = '') => $h($link[$k] ?? $d);
$own = json_decode((string) ($link['texts'] ?? ''), true);
$own = is_array($own) ? $own : [];
$target = $link['target'] ?? 'home-register';
?>
<div class="grid2">
    <div>
        <label for="<?= $p ?>-code">Kód odkazu <span class="req">*</span></label>
        <?php if (!empty($codeLocked)): ?>
            <input type="text" id="<?= $p ?>-code" value="<?= $v('code') ?>" disabled>
            <p class="muted" style="margin:4px 0 0">Odkaz už má návštěvy – kód se nemění (může být vytištěný nebo zveřejněný).</p>
        <?php else: ?>
            <input type="text" id="<?= $p ?>-code" name="code" required maxlength="30" pattern="[a-z][a-z0-9\-]{1,29}" value="<?= $v('code') ?>"
                   placeholder="např. skoda, up-brno, vp-billboard" autocapitalize="none" spellcheck="false">
            <p class="muted" style="margin:4px 0 0">Malá písmena bez diakritiky, číslice a pomlčka. Lidé ho opisují z plakátu – krátký a čitelný.
                Po první návštěvě se už nemění.</p>
        <?php endif; ?>
    </div>
    <div>
        <label for="<?= $p ?>-name">Název <span class="req">*</span></label>
        <input type="text" id="<?= $p ?>-name" name="name" required maxlength="200" value="<?= $v('name') ?>" placeholder="např. Škoda Auto – kampaň 2027">
    </div>
    <div>
        <label for="<?= $p ?>-owner">Od koho <span class="req">*</span></label>
        <select id="<?= $p ?>-owner" name="owner_type" required>
            <option value="">– vyberte –</option>
            <?php foreach (PromoLinkService::OWNER_TYPES as $k => $label): ?>
                <option value="<?= $h($k) ?>" <?= ($link['owner_type'] ?? '') === $k ? 'selected' : '' ?>><?= $h($label) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label for="<?= $p ?>-company">Firma (vystavovatel)</label>
        <select id="<?= $p ?>-company" name="company_id">
            <option value="">– žádná –</option>
            <?php foreach ($companies as $c): ?>
                <option value="<?= (int) $c['id'] ?>" <?= (int) ($link['company_id_fk'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= $h($c['name']) ?> (<?= $h($c['version_fk']) ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<label for="<?= $p ?>-target">Cíl – kam odkaz vede <span class="req">*</span></label>
<select id="<?= $p ?>-target" name="target" required onchange="document.getElementById('<?= $p ?>-tp').style.display = this.value === '<?= PromoConfig::TARGET_PAGE ?>' ? '' : 'none'">
    <?php foreach ($config->targets as $k => $t): ?>
        <option value="<?= $h($k) ?>" <?= $target === $k ? 'selected' : '' ?>><?= $h($t['label'] ?? $k) ?></option>
    <?php endforeach; ?>
</select>
<div id="<?= $p ?>-tp" style="<?= $target === PromoConfig::TARGET_PAGE ? '' : 'display:none' ?>">
    <label for="<?= $p ?>-path">Adresa stránky tohoto webu</label>
    <input type="text" id="<?= $p ?>-path" name="target_path" maxlength="500" value="<?= $v('target_path') ?>"
           placeholder="zkopírujte z adresního řádku prohlížeče, např. https://…/web/v1/page/item/…" spellcheck="false">
    <p class="muted" style="margin:4px 0 0">Jen stránky tohoto webu. Cíl jde kdykoli změnit – vytištěné QR kódy a zveřejněné odkazy pak vedou na nový cíl.</p>
</div>
<label for="<?= $p ?>-note">Poznámka (kampaň, médium, kde je odkaz zveřejněný)</label>
<textarea id="<?= $p ?>-note" name="note" rows="2" maxlength="1000"><?= $v('note') ?></textarea>
<?php if ($link !== null): ?>
<p class="check"><input type="checkbox" id="<?= $p ?>-active" name="active" value="1" <?= (int) $link['active'] ? 'checked' : '' ?>>
    <label for="<?= $p ?>-active">Aktivní – vypnutý odkaz vede na úvodní stránku a nic nepočítá</label></p>
<?php endif; ?>
<details>
    <summary>Texty ke kopírování (výchozí z konfigurace; <code>{url}</code> = adresa odkazu s kanálem, <code>{name}</code> = název)</summary>
    <?php foreach (['social' => 'Příspěvek na sociální síť', 'mail' => 'E-mail', 'web' => 'Text odkazu na webu'] as $k => $label): ?>
        <label for="<?= $p ?>-t-<?= $k ?>"><?= $h($label) ?></label>
        <textarea id="<?= $p ?>-t-<?= $k ?>" name="texts[<?= $k ?>]" rows="<?= $k === 'mail' ? 6 : 2 ?>" maxlength="2000"
                  placeholder="<?= $h($config->texts[$k] ?? '') ?>"><?= $h($own[$k] ?? '') ?></textarea>
    <?php endforeach; ?>
</details>
