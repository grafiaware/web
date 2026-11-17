<?php
/** G0 (#116): jedna instituce (úprava, smazání) nebo formulář nové instituce (bez $id). */
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Events\Middleware\Events\Controler\EventControler;

/** @var PhpTemplateRendererInterface $this */
/** @var array $types id => název typu */
$h = fn($v) => htmlspecialchars((string) $v);
$isNew = !isset($id);
$action = $isNew ? 'events/v1/institution' : 'events/v1/institution/' . (int) $id;
$selectedType = $institutionTypeId ?? null;
?>
<form class="ui form segment" method="POST" action="<?= $action ?>">
    <div class="two fields">
        <div class="field">
            <label>Název instituce</label>
            <input type="text" name="institutionName" required maxlength="100" value="<?= $h($name ?? '') ?>">
        </div>
        <div class="field">
            <label>Typ instituce</label>
            <select name="selectInstitutionTypeId">
                <option value="<?= $h(EventControler::NULL_VALUE_nahradni) ?>">– bez typu –</option>
                <?php foreach ($types as $typeId => $typeName): ?>
                    <option value="<?= (int) $typeId ?>" <?= (string) $typeId === (string) $selectedType ? 'selected' : '' ?>><?= $h($typeName) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <button class="ui primary button" type="submit"><?= $isNew ? 'Přidat' : 'Uložit' ?></button>
    <?php if (!$isNew): ?>
        <button class="ui button" type="submit" formaction="<?= $action ?>/remove"
                onclick="return confirm('Smazat instituci? Nejde smazat instituci, kterou používají obsahy.')">Smazat</button>
    <?php endif; ?>
</form>
