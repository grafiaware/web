<?php
/**
 * G0 (#116): harmonogram vystavovatele – eventy jeho instituce (instituce se jmenuje stejně jako firma) s počtem
 * přihlášených, z DB přes ProgramViewModel. Jen se zapnutým vp.program. Vstup: $representativeContext, $container.
 */
use Site\ConfigurationCache;
use Application\Feature\FeatureFlags;
use Events\Middleware\Events\ViewModel\ProgramViewModel;
use Pes\View\Renderer\PhpTemplateRendererInterface;

/** @var PhpTemplateRendererInterface $this */

if (FeatureFlags::fromConfiguration()->isEnabled('vp.program') && !empty($representativeContext['nameCompany'])) {
    /** @var ProgramViewModel $programViewModel */
    $programViewModel = $container->get(ProgramViewModel::class);
    $event = $programViewModel->getTimeline(null, null, false, false, $representativeContext['nameCompany'], true);
    $showEnrolledCount = true;
?>
<p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll">Náš program</p>
<?php if ($event) { ?>
    <?php include ConfigurationCache::eventTemplates()['templates'] . "timeline-leafs/content/timeline.php"; ?>
<?php } else { ?>
    <p>V programu zatím nemáte žádnou akci.</p>
<?php } ?>
<?php
}
