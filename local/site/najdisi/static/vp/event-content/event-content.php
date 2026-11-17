<?php
/** G0 (#116): jeden obsah eventu (úprava, smazání) nebo formulář nového obsahu (bez $id). */
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Events\Middleware\Events\Controler\EventControler;

/** @var PhpTemplateRendererInterface $this */
/** @var array $types id => název typu obsahu */
/** @var array $institutions id => název instituce */
$h = fn($v) => htmlspecialchars((string) $v);
$isNew = !isset($id);
$action = $isNew ? 'events/v1/eventcontent' : 'events/v1/eventcontent/' . (int) $id;
$selected = fn($a, $b) => (string) $a === (string) $b ? 'selected' : '';
?>
<form class="ui form segment" method="POST" action="<?= $action ?>">
    <div class="two fields">
        <div class="field">
            <label>Typ obsahu</label>
            <select name="selectContentTypeId" required>
                <option value="">– vyberte –</option>
                <?php foreach ($types as $typeId => $typeName): ?>
                    <option value="<?= (int) $typeId ?>" <?= $selected($typeId, $contentTypeId ?? null) ?>><?= $h($typeName) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label>Instituce</label>
            <select name="selectInstitutionId">
                <option value="<?= $h(EventControler::NULL_VALUE_nahradni) ?>">– bez instituce –</option>
                <?php foreach ($institutions as $instId => $instName): ?>
                    <option value="<?= (int) $instId ?>" <?= $selected($instId, $selectedInstitutionId ?? null) ?>><?= $h($instName) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="field">
        <label>Název</label>
        <input type="text" name="title" required maxlength="200" value="<?= $h($title ?? '') ?>">
    </div>
    <div class="field">
        <label>Popis</label>
        <textarea name="perex" maxlength="500" rows="3"><?= $h($perex ?? '') ?></textarea>
    </div>
    <div class="field">
        <label>Vystupující</label>
        <input type="text" name="party" maxlength="200" value="<?= $h($party ?? '') ?>">
    </div>
    <button class="ui primary button" type="submit"><?= $isNew ? 'Přidat' : 'Uložit' ?></button>
    <?php if (!$isNew): ?>
        <button class="ui button" type="submit" formaction="<?= $action ?>/remove"
                onclick="return confirm('Smazat obsah? Eventy s tímto obsahem zůstanou bez obsahu a zmizí z programu.')">Smazat</button>
    <?php endif; ?>
</form>
