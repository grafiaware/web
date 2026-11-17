<?php
/**
 * G0 (#116): Můj harmonogram – eventy, na které je přihlášený uživatel přihlášen, i proběhlé (z DB přes ProgramViewModel).
 * Zobrazí se jen se zapnutým vp.program. Vstup: $loginName, $container (z profilu návštěvníka).
 */
use Site\ConfigurationCache;
use Application\Feature\FeatureFlags;
use Events\Middleware\Events\ViewModel\ProgramViewModel;
use Pes\View\Renderer\PhpTemplateRendererInterface;

/** @var PhpTemplateRendererInterface $this */

if (FeatureFlags::fromConfiguration()->isEnabled('vp.program') && isset($loginName)) {
    /** @var ProgramViewModel $programViewModel */
    $programViewModel = $container->get(ProgramViewModel::class);
    // jen moje, s odhlášením; i proběhlé akce, které si naplánoval (rozhodl Petr 17. 11. 2026), u nich bez tlačítek
    $event = $programViewModel->getTimeline(null, $loginName, true, true, null, true);
?>
<div class="title">
    <i class="dropdown icon"></i>
    Můj harmonogram
</div>
<div class="content">
    <?php if ($event) { ?>
        <?php include ConfigurationCache::eventTemplates()['templates'] . "timeline-leafs/content/timeline.php"; ?>
    <?php } else { ?>
        <p>Zatím nejste přihlášeni na žádnou akci. Přihlásit se můžete v programu veletrhu.</p>
    <?php } ?>
</div>
<?php
}
