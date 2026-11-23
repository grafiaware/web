<?php
/**
 * R1 (#48): veřejný formulář rezervace míst na přednáškách pro školy (bez přihlášení). Zadání docs/vp/zadani/R1.md.
 * Nabízí nejnovější otevřenou nabídku (SchoolReservationService::publicOffer()). Odeslání: POST events/v1/reservation/book/:code,
 * po úspěchu přesměrování na stránku správy rezervace, při chybě zpět sem s hláškou (vyplněné údaje obnoví sessionStorage).
 */
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Application\Feature\FeatureFlags;
use Component\ViewModel\StatusViewModel;
use Events\Service\Reservation\SchoolReservationService;
use Events\Service\Lottery\AuthLookup;

/** @var PhpTemplateRendererInterface $this */

if (!FeatureFlags::fromConfiguration()->isEnabled('vp.rezervace')) {
    echo '<p class="ui segment">Rezervace nejsou zapnuté.</p>';
    return;
}
/** @var SchoolReservationService $reservationService */
$reservationService = $container->get(SchoolReservationService::class);
$reservationService->housekeeping();
$offer = $reservationService->publicOffer();
$state = SchoolReservationService::bookingState($offer, $reservationService->now());
$h = fn($v) => htmlspecialchars((string) $v);

$values = [];
$loginName = (string) $container->get(StatusViewModel::class)->getUserLoginName();
if ($loginName !== '') {
    try {
        $reservationService->linkToLogin($loginName, $container->get(AuthLookup::class)->emailOf($loginName));
    } catch (\Throwable $e) {
        // bez párování s účtem formulář funguje dál
    }
    $values = $reservationService->prefill($loginName);   // bez telefonu
}
$lectures = $offer !== null && $state['open'] ? $reservationService->lectures((int) $offer['id']) : [];
$bookable = array_filter($lectures, fn($l) => $l['bookable'] && $l['free'] > 0);
$pagesDir = \Events\Middleware\Events\Controler\Reservation\ReservationControlerAbstract::PAGES_DIR;
?>
<div class="ui segment vp-rezervace-skol">
    <h2 class="ui header"><?= $h($offer['name'] ?? 'Rezervace přednášek pro školy') ?></h2>
    <?php if (!$state['open']): ?>
        <p class="ui message"><?= $h($state['message']) ?></p>
    <?php elseif (!$bookable): ?>
        <p class="ui message">Všechny přednášky jsou teď obsazené. Napište prosím organizátorovi
            <?= $h($reservationService->config()->organizerContact) ?> – místa se mohou uvolnit.</p>
    <?php else: ?>
        <p>Zarezervujte své skupině místa na přednášce veletrhu (<?= $h(SchoolReservationService::fmt($offer['event_date'], 'j. n. Y')) ?>).
            Kapacita přednášky zahrnuje žáky i pedagogy. Potvrzení s odkazem na změnu nebo zrušení rezervace vám pošleme e-mailem.
            Registrace na webu není nutná.</p>
        <form class="ui form" id="vp-rezervace-skol-form" method="POST" action="events/v1/reservation/book/<?= $h($offer['code']) ?>">
            <?= $this->insert($pagesDir . '_fields.php', ['mode' => 'public', 'values' => $values, 'lectures' => $lectures,
                'config' => $reservationService->config(), 'idPrefix' => 'rs']) ?>
            <button class="ui primary button" type="submit">Rezervovat</button>
            <p style="margin-top:8px"><span style="color:#b00020">*</span> povinné údaje</p>
        </form>
        <script>
        // Při chybě (plná přednáška, překlep) se stránka načte znovu – vyplněné údaje vrátíme z sessionStorage tohoto okna.
        (function () {
            var form = document.getElementById('vp-rezervace-skol-form'), key = 'vp-rezervace-skol';
            if (!form) { return; }
            var skip = {consent: 1, phone_consent: 1, web_skoly: 1};
            try {
                var saved = JSON.parse(sessionStorage.getItem(key) || 'null');
                if (saved) {
                    Object.keys(saved).forEach(function (name) {
                        var el = form.elements[name];
                        if (el && !skip[name] && el.type !== 'checkbox') { el.value = saved[name]; }
                    });
                }
            } catch (e) {}
            form.addEventListener('submit', function () {
                var data = {};
                Array.prototype.forEach.call(form.elements, function (el) {
                    if (el.name && !skip[el.name] && el.type !== 'checkbox') { data[el.name] = el.value; }
                });
                try { sessionStorage.setItem(key, JSON.stringify(data)); } catch (e) {}
            });
        })();
        </script>
    <?php endif; ?>
</div>
