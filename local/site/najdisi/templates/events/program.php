<?php
/**
 * G0 (#116): program veletrhu z DB (ProgramViewModel) – sdílené pro statické stránky vp/program, vp/program-prednasky,
 * vp/program-poradny (další typ = nová statická stránka se 4 řádky).
 *
 * Vstup od statické stránky: $programType (kód typu obsahu, null = všechny), $programLayout ('boxes' | 'column'),
 * $headline, $perex. Přepínač vp.program.
 */
use Site\ConfigurationCache;
use Application\Feature\FeatureFlags;
use Component\ViewModel\StatusViewModel;
use Events\Middleware\Events\ViewModel\ProgramViewModel;
use Pes\View\Renderer\PhpTemplateRendererInterface;

/** @var PhpTemplateRendererInterface $this */

if (!FeatureFlags::fromConfiguration()->isEnabled('vp.program')) {
    echo '<p class="ui segment">Program zatím není zveřejněn.</p>';
    return;
}
// #191: program potřebuje kontejner modulu events (EventsModelContainerConfigurator). Položka menu jiného modulu
// (red|static) ho nemá – místo Fatal error srozumitelná hláška pro toho, kdo položku zakládá.
if (!$container->has(ProgramViewModel::class)) {
    echo '<p class="ui orange segment">Program nelze zobrazit: stránka musí být položka menu typu statická stránka '
        . 'modulu <b>events</b> (ne red). Obraťte se na správce webu.</p>';
    return;
}
/** @var StatusViewModel $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
/** @var ProgramViewModel $programViewModel */
$programViewModel = $container->get(ProgramViewModel::class);
$event = $programViewModel->getTimeline($programType ?? null, $statusViewModel->getUserLoginName(), true);

$headline = $headline ?? 'Program veletrhu';
$perex = ($perex ?? '') . ($statusViewModel->isUserLoggedIn()
    ? ' Na akce se můžete přihlásit – najdete je pak ve svém profilu v harmonogramu.'
    : ' Po přihlášení ke svému účtu se můžete na akce přihlásit a mít je v harmonogramu.');
$footer = $event ? '' : 'Program připravujeme, zkuste to prosím později.';

include ConfigurationCache::eventTemplates()['templates'] . (($programLayout ?? 'boxes') === 'column' ? 'timecolumn.php' : 'timeline-boxes.php');
