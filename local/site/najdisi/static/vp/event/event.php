<?php
/** G0 (#116): jeden event (úprava, smazání) nebo formulář nového eventu (bez $id). */
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Events\Middleware\Events\Controler\EventControler;

/** @var PhpTemplateRendererInterface $this */
/** @var array $contents id => „typ: název“ obsahu */
/** @var array $links id => „fáze – adresa“ odkazu */
$h = fn($v) => htmlspecialchars((string) $v);
$isNew = !isset($id);
$action = $isNew ? 'events/v1/event' : 'events/v1/event/' . (int) $id;
$selected = fn($a, $b) => (string) $a === (string) $b ? 'selected' : '';
$none = EventControler::NULL_VALUE_nahradni;
/** @var \DateTime|null $start */
/** @var \DateTime|null $end */
$date = isset($start) ? $start->format('Y-m-d') : ($defaultDate ?? '');
$linkSelect = function (string $name, $current) use ($links, $h, $selected, $none): string {
    $html = '<select name="' . $name . '"><option value="' . $h($none) . '">– žádný –</option>';
    foreach ($links as $linkId => $linkText) {
        $html .= '<option value="' . (int) $linkId . '" ' . $selected($linkId, $current) . '>' . $h($linkText) . '</option>';
    }
    return $html . '</select>';
};
?>
<form class="ui form segment" method="POST" action="<?= $action ?>">
    <?php if (!$isNew): ?>
        <p><b><?= $h($start ? $start->format('j. n. Y G:i') : '') ?>–<?= $h($end ? $end->format('G:i') : '') ?></b>
            <?= $h($contents[$contentId] ?? '(bez obsahu)') ?> · přihlášeno: <?= (int) $enrolled ?></p>
    <?php endif; ?>
    <div class="fields">
        <div class="four wide field">
            <label>Datum</label>
            <input type="date" name="date" required value="<?= $h($date) ?>">
        </div>
        <div class="two wide field">
            <label>Od</label>
            <input type="text" name="startTime" required inputmode="numeric" placeholder="10:30"
                   pattern="([01]?[0-9]|2[0-3])([:.]?[0-5][0-9])?" value="<?= $h(isset($start) ? $start->format('G:i') : '') ?>">
        </div>
        <div class="two wide field">
            <label>Do</label>
            <input type="text" name="endTime" required inputmode="numeric" placeholder="11:15"
                   pattern="([01]?[0-9]|2[0-3])([:.]?[0-5][0-9])?" value="<?= $h(isset($end) ? $end->format('G:i') : '') ?>">
        </div>
        <div class="eight wide field">
            <label>Obsah</label>
            <select name="selectEventContentId" required>
                <option value="">– vyberte –</option>
                <?php foreach ($contents as $cId => $cText): ?>
                    <option value="<?= (int) $cId ?>" <?= $selected($cId, $contentId ?? null) ?>><?= $h($cText) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="fields">
        <div class="six wide field">
            <label>Odkaz pro přihlášku (externí)</label>
            <?= $linkSelect('selectEnrollLinkId', $enrollLinkId ?? null) ?>
        </div>
        <div class="six wide field">
            <label>Odkaz pro vstup (online)</label>
            <?= $linkSelect('selectEnterLinkId', $enterLinkId ?? null) ?>
        </div>
        <div class="four wide field">
            <label>Zveřejnit</label>
            <div class="ui checkbox">
                <input type="checkbox" name="published" value="1" <?= !empty($published) ? 'checked' : '' ?>><label>na webu</label>
            </div>
        </div>
    </div>
    <button class="ui primary button" type="submit"><?= $isNew ? 'Přidat' : 'Uložit' ?></button>
    <?php if (!$isNew): ?>
        <button class="ui button" type="submit" formaction="<?= $action ?>/remove"
                onclick="return confirm('Smazat event?')">Smazat</button>
    <?php endif; ?>
</form>
