<?php
/**
 * F2 (#47, docs/vp/zadani/F1.md): promo kit „Pozvěte lidi na veletrh“ v profilu vystavovatele – odkaz firmy, podklady
 * ke zkopírování po kanálech, počet návštěv a registrací z odkazu. Odkaz firmy se založí při prvním zobrazení
 * (kód z názvu firmy, výchozí cíl úvod s oknem registrace); cíl a kód mění media manager.
 * F3: se zapnutým vp.qr-plakaty navíc QR kód odkazu (PNG) a podklad plakátu PDF se svými údaji (pro vlastního grafika).
 * Vkládá template.php při zapnutém přepínači vp.odkazy. Očekává $container, $companyE, $representativeFromStatus.
 * Adresy jsou v šabloně s {BASE}, skript je doplní o adresu webu (document.baseURI).
 */
use Events\Middleware\Events\Controler\Promo\PromoControlerAbstract;
use Events\Service\Promo\PromoAuthLookup;
use Events\Service\Promo\PromoLinkService;

/** @var PromoLinkService $promoLinks */
$promoLinks = $container->get(PromoLinkService::class);
try {
    $promoLink = $promoLinks->companyLink((int) $companyE->getId(), (string) $companyE->getName(),
        (string) $representativeFromStatus->getLoginLoginName());
} catch (\Throwable $e) {
    error_log('F2 promo kit failed: ' . $e->getMessage());
    $promoLink = null;
}
if ($promoLink !== null):
    try {
        $promoRegs = $container->get(PromoAuthLookup::class)->registrationCounts(null, null);
    } catch (\Throwable $e) {
        $promoRegs = [];
    }
    $promoRow = PromoLinkService::overview([$promoLink], $promoLinks->visitsByChannel(null, null, (int) $promoLink['id']), $promoRegs)['links'][0] ?? null;
    $promoCopy = (new \Pes\View\ViewFactory())->phpTemplateView(PromoControlerAbstract::PAGES_DIR . '_copy.php',
        ['rows' => $promoLinks->copyRows($promoLink, '{BASE}'), 'idPrefix' => 'kit'])
        ->setRenderer(new \Pes\View\Renderer\PhpTemplateRenderer());
?>
<section class="vp-promo-kit">
    <div class="ui segment">
        <h3 class="ui header">Pozvěte lidi na veletrh</h3>
        <p>Váš odkaz na veletrh: <b class="vp-abs" data-rel="events/v1/r/<?= htmlspecialchars($promoLink['code']) ?>">events/v1/r/<?= htmlspecialchars($promoLink['code']) ?></b>
            <?= (int) $promoLink['active'] ? '' : '<span class="ui red text">(odkaz je vypnutý)</span>' ?></p>
        <p>Dejte ho na svůj web, do příspěvků na sociálních sítích, do e-mailů a na plakáty – ideálně mimo svůj areál, ať přijdou
            i lidé, kteří vás ještě neznají. Pro každé místo použijte adresu s kanálem níže, uvidíte, odkud lidé přišli.</p>
        <?php if ($promoRow !== null): ?>
        <p>Z vašeho odkazu zatím: <b><?= (int) $promoRow['visits'] ?></b> návštěv, <b><?= (int) $promoRow['registered'] ?></b> registrací,
            z toho <b><?= (int) $promoRow['confirmed'] ?></b> potvrzených.</p>
        <?php endif; ?>
        <div class="vp-promo-copy"><?= $promoCopy ?></div>
        <?php if (\Application\Feature\FeatureFlags::fromConfiguration()->isEnabled('vp.qr-plakaty')): ?>
        <div class="vp-promo-poster" data-font-regular="public/assets/fonts/liberation-sans/LiberationSans-Regular.ttf"
             data-font-bold="public/assets/fonts/liberation-sans/LiberationSans-Bold.ttf">
            <h4 class="ui header">QR kód a plakát</h4>
            <p><a class="ui small button" href="events/v1/promo/<?= (int) $promoLink['id'] ?>/qr.png">QR kód (PNG)</a>
                <span class="maly text">na web, do prezentace nebo na obrazovku</span></p>
            <p><?php foreach (\Events\Service\Promo\PosterDataService::FORMATS as $fmt): ?>
                <button type="button" class="ui small orange button" data-poster-url="events/v1/promo/<?= (int) $promoLink['id'] ?>/poster"
                        data-poster-format="<?= htmlspecialchars($fmt) ?>">Podklad plakátu PDF <?= htmlspecialchars($fmt) ?></button>
            <?php endforeach; ?></p>
            <p class="maly text">Podklad pro vašeho grafika: QR kód a texty (název, adresa, web a sociální sítě, které máte v profilu
                zveřejněné) jako samostatné objekty bez designu – grafik z nich plakát sestaví v Illustratoru, InDesignu apod.</p>
            <p data-poster-status class="maly text"></p>
        </div>
        <script src="public/assets/lib/jspdf-4.2.1.umd.min.js"></script>
        <script src="public/assets/lib/svg2pdf-2.9.0.umd.min.js"></script>
        <script src="public/assets/lib/qrcode-generator-2.0.4.js"></script>
        <script src="public/site/common/js/promo-poster-pdf.js"></script>
        <script>document.querySelectorAll('.vp-promo-poster').forEach(PromoPosterPdf.bind);</script>
        <?php endif; ?>
        <p class="maly text">Kam odkaz vede, nastavuje organizátor veletrhu. Změnu domluvte s ním.</p>
    </div>
    <style>
        .vp-promo-kit .copy { display: flex; gap: 6px; align-items: flex-start; margin: 4px 0; }
        .vp-promo-kit .copy > :first-child { flex: 1 1 auto; min-width: 0; }
        .vp-promo-kit .copy input, .vp-promo-kit .copy textarea { width: 100%; font-family: monospace; font-size: 13px; padding: 6px; border: 1px solid #ccc; border-radius: 4px; background: #fafafa; }
        .vp-promo-kit .copy button { padding: 6px 10px; border: 0; border-radius: 4px; background: #e8590c; color: #fff; cursor: pointer; }
        .vp-promo-kit .copy button.copied { background: #1b7a2b; }
        .vp-promo-kit details { margin: 6px 0; } .vp-promo-kit summary { cursor: pointer; } .vp-promo-kit .muted { color: #666; font-size: 90%; }
    </style>
    <script>
    (function () {
        var kit = document.currentScript.closest('.vp-promo-kit');
        // stránka webu má <base href> = kořen webu; samostatně načtená šablona (events/v1/static/…) ho nemá
        var base = document.querySelector('base[href]') ? document.baseURI : location.href.replace(/events\/v1\/.*$/, '');
        kit.querySelectorAll('input, textarea').forEach(function (el) { el.value = el.value.split('{BASE}').join(base); });
        kit.querySelectorAll('.vp-abs').forEach(function (el) { el.textContent = new URL(el.getAttribute('data-rel'), base).toString(); });
        kit.addEventListener('click', function (e) {
            var b = e.target.closest('button[data-copy]');
            if (!b) { return; }
            e.preventDefault();
            var el = document.getElementById(b.getAttribute('data-copy'));
            var done = function () { var t = b.textContent; b.textContent = 'Zkopírováno'; b.classList.add('copied');
                setTimeout(function () { b.textContent = t; b.classList.remove('copied'); }, 1500); };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(el.value).then(done, function () { el.select(); document.execCommand('copy'); done(); });
            } else { el.select(); document.execCommand('copy'); done(); }
        });
    })();
    </script>
</section>
<?php endif; ?>
