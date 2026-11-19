<?php
/** G0 (#116): jeden odkaz eventu (úprava, smazání) nebo formulář nového odkazu (bez $id). */
use Pes\View\Renderer\PhpTemplateRendererInterface;

/** @var PhpTemplateRendererInterface $this */
/** @var array $phases id => text tlačítka */
$h = fn($v) => htmlspecialchars((string) $v);
$isNew = !isset($id);
$action = $isNew ? 'events/v1/eventlink' : 'events/v1/eventlink/' . (int) $id;
$selectedPhase = $linkPhaseId ?? null;
?>
<form class="ui form segment" method="POST" action="<?= $action ?>">
    <div class="fields">
        <div class="ten wide field">
            <label>Adresa (URL)</label>
            <input type="url" name="href" required maxlength="255" placeholder="https://…" value="<?= $h($href ?? '') ?>">
        </div>
        <div class="four wide field">
            <label>Fáze (text tlačítka)</label>
            <select name="eventLinkPhaseId" required>
                <option value="">– vyberte –</option>
                <?php foreach ($phases as $phaseId => $phaseText): ?>
                    <option value="<?= (int) $phaseId ?>" <?= (string) $phaseId === (string) $selectedPhase ? 'selected' : '' ?>><?= $h($phaseText) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="two wide field">
            <label>Zobrazit</label>
            <div class="ui checkbox">
                <input type="checkbox" name="show" value="1" <?= ($show ?? 1) ? 'checked' : '' ?>><label>ano</label>
            </div>
        </div>
    </div>
    <button class="ui primary button" type="submit"><?= $isNew ? 'Přidat' : 'Uložit' ?></button>
    <?php if (!$isNew): ?>
        <button class="ui button" type="submit" formaction="<?= $action ?>/remove"
                onclick="return confirm('Smazat odkaz? Eventy s tímto odkazem zůstanou bez odkazu.')">Smazat</button>
    <?php endif; ?>
</form>
