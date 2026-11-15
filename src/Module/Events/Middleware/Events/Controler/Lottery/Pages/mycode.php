<?php
/** D1: stránka „Můj kód“ – QR kód návštěvníka; service worker ji uloží pro zobrazení bez připojení. */
/** @var string $name */
/** @var string $code */
/** @var string $qrDataUri */
/** @var array $joined */
/** @var string $swUrl */
?>
<div class="card" style="text-align:center">
    <h1>Můj kód pro losování</h1>
    <p><b><?= htmlspecialchars($name) ?></b></p>
    <img src="<?= $qrDataUri ?>" alt="QR kód" style="width: 100%; max-width: 320px; image-rendering: pixelated;" />
    <p class="muted">Kód: <?= htmlspecialchars(implode(' ', str_split($code, 4))) ?></p>
    <?php if (!$joined): ?>
        <p class="bad">Zatím nejste zapojeni do žádného losování – zapojte se ve svém profilu.</p>
    <?php else: ?>
        <p class="ok">Zapojeno: <?= htmlspecialchars(implode(', ', array_column($joined, 'name'))) ?></p>
    <?php endif; ?>
</div>
<div class="card">
    <h2>Na místě bez signálu</h2>
    <p>Kód se nemění. Uložte si ho předem:</p>
    <p><a class="btn" href="events/v1/mycode/image" download="veletrh-qr-kod.png">Uložit QR do telefonu</a></p>
    <p>nebo udělejte snímek obrazovky. Tuto stránku si přidejte do záložek – <span id="offline-state">po prvním otevření s připojením se otevře i bez internetu</span>.</p>
    <p class="muted">U vchodu kód ukažte obsluze, na infostánku si los prodloužíte.</p>
</div>
<script>
(function () {
    var s = document.getElementById('offline-state');
    if (!('serviceWorker' in navigator)) { s.textContent = 'tento prohlížeč neumí stránku uložit, uložte si proto obrázek nebo snímek obrazovky'; return; }
    function controlled() {
        // po první registraci stránku ještě neřídí service worker – počkat na převzetí
        return navigator.serviceWorker.controller ? Promise.resolve() : new Promise(function (resolve) {
            navigator.serviceWorker.addEventListener('controllerchange', function () { resolve(); }, {once: true});
        });
    }
    navigator.serviceWorker.register(<?= json_encode($swUrl) ?>).then(function () {
        return navigator.serviceWorker.ready;
    }).then(controlled).then(function () {
        // stránku a obrázek načíst přes service worker, aby byly v úložišti
        return Promise.all([fetch(location.href, {credentials: 'same-origin'}), fetch('events/v1/mycode/image', {credentials: 'same-origin'})]);
    }).then(function () {
        s.textContent = 'stránka je uložená a otevře se i bez internetu';
    }).catch(function () { s.textContent = 'stránku se nepodařilo uložit, uložte si obrázek nebo snímek obrazovky'; });
})();
</script>
