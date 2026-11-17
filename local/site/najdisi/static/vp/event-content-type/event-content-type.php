<?php
/** G0 (#116): jeden typ obsahu eventu (úprava, smazání) nebo formulář nového typu (bez $id). */
use Pes\View\Renderer\PhpTemplateRendererInterface;

/** @var PhpTemplateRendererInterface $this */
$h = fn($v) => htmlspecialchars((string) $v);
$isNew = !isset($id);
$action = $isNew ? 'events/v1/eventcontenttype' : 'events/v1/eventcontenttype/' . (int) $id;
?>
<form class="ui form segment" method="POST" action="<?= $action ?>">
    <div class="two fields">
        <div class="field">
            <label>Kód typu</label>
            <input type="text" name="type" required maxlength="45" value="<?= $h($type ?? '') ?>">
        </div>
        <div class="field">
            <label>Název</label>
            <input type="text" name="name" maxlength="45" value="<?= $h($name ?? '') ?>">
        </div>
    </div>
    <button class="ui primary button" type="submit"><?= $isNew ? 'Přidat' : 'Uložit' ?></button>
    <?php if (!$isNew): ?>
        <button class="ui button" type="submit" formaction="<?= $action ?>/remove"
                onclick="return confirm('Smazat typ obsahu? Nejde smazat typ, který používají obsahy.')">Smazat</button>
    <?php endif; ?>
</form>
