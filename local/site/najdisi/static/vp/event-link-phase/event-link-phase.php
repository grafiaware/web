<?php
/** G0 (#116): jedna fáze odkazu (úprava, smazání) nebo formulář nové fáze (bez $id). */
use Pes\View\Renderer\PhpTemplateRendererInterface;

/** @var PhpTemplateRendererInterface $this */
$h = fn($v) => htmlspecialchars((string) $v);
$isNew = !isset($id);
$action = $isNew ? 'events/v1/eventlinkphase' : 'events/v1/eventlinkphase/' . (int) $id;
?>
<form class="ui form segment" method="POST" action="<?= $action ?>">
    <div class="field">
        <label>Text tlačítka</label>
        <input type="text" name="eventLinkPhaseText" required maxlength="100" value="<?= $h($text ?? '') ?>">
    </div>
    <button class="ui primary button" type="submit"><?= $isNew ? 'Přidat' : 'Uložit' ?></button>
    <?php if (!$isNew): ?>
        <button class="ui button" type="submit" formaction="<?= $action ?>/remove"
                onclick="return confirm('Smazat fázi odkazu? Nejde smazat fázi, kterou používají odkazy.')">Smazat</button>
    <?php endif; ?>
</form>
