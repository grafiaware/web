<?php
/** G0 (#116): jeden typ instituce (úprava, smazání) nebo formulář nového typu (bez $id). */
use Pes\View\Renderer\PhpTemplateRendererInterface;

/** @var PhpTemplateRendererInterface $this */
$h = fn($v) => htmlspecialchars((string) $v);
$isNew = !isset($id);
$action = $isNew ? 'events/v1/institutiontype' : 'events/v1/institutiontype/' . (int) $id;
?>
<form class="ui form segment" method="POST" action="<?= $action ?>">
    <div class="field">
        <label>Typ instituce</label>
        <input type="text" name="institutionType" required maxlength="45" value="<?= $h($institutionType ?? '') ?>">
    </div>
    <button class="ui primary button" type="submit"><?= $isNew ? 'Přidat' : 'Uložit' ?></button>
    <?php if (!$isNew): ?>
        <button class="ui button" type="submit" formaction="<?= $action ?>/remove"
                onclick="return confirm('Smazat typ instituce?')">Smazat</button>
    <?php endif; ?>
</form>
