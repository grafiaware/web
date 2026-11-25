<?php
/**
 * G0 (#116): správa eventů – kdy (den, čas od–do), co (obsah), zveřejnění a odkazy (přihláška, vstup online)
 * – jen events_administrator. Akce: EventControler (events/v1/event). Seřazeno podle začátku, s počtem přihlášených.
 */
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Core\Text\Html;
use Component\ViewModel\StatusViewModel;
use Access\Enum\RoleEnum;
use Events\Model\Repository\EventRepo;
use Events\Model\Repository\EventRepoInterface;
use Events\Model\Repository\EventContentRepo;
use Events\Model\Repository\EventContentRepoInterface;
use Events\Model\Repository\EventContentTypeRepo;
use Events\Model\Repository\EventContentTypeRepoInterface;
use Events\Model\Repository\EventLinkRepo;
use Events\Model\Repository\EventLinkRepoInterface;
use Events\Model\Repository\EventLinkPhaseRepo;
use Events\Model\Repository\EventLinkPhaseRepoInterface;
use Events\Model\Repository\EnrollRepo;
use Events\Model\Repository\EnrollRepoInterface;

/** @var PhpTemplateRendererInterface $this */

/** @var StatusViewModel $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
if ($statusViewModel->getUserRole() != RoleEnum::EVENTS_ADMINISTRATOR) {
    echo Html::p("Stránka je určena pouze pro administraci programu.", ["class" => "ui orange segment"]);
    return;
}

/** @var EventContentTypeRepoInterface $eventContentTypeRepo */
$eventContentTypeRepo = $container->get(EventContentTypeRepo::class);
$typeNames = [];
foreach ($eventContentTypeRepo->findAll() as $contentType) {
    $typeNames[$contentType->getId()] = $contentType->getName() ?: $contentType->getType();
}
/** @var EventContentRepoInterface $eventContentRepo */
$eventContentRepo = $container->get(EventContentRepo::class);
$contents = [];
foreach ($eventContentRepo->findAll() as $content) {
    $type = $typeNames[$content->getEventContentTypeIdFk()] ?? '';
    $contents[$content->getId()] = ($type ? $type . ': ' : '') . $content->getTitle();
}
asort($contents, SORT_LOCALE_STRING);
/** @var EventLinkPhaseRepoInterface $eventLinkPhaseRepo */
$eventLinkPhaseRepo = $container->get(EventLinkPhaseRepo::class);
$phases = [];
foreach ($eventLinkPhaseRepo->findAll() as $phase) {
    $phases[$phase->getId()] = $phase->getText();
}
/** @var EventLinkRepoInterface $eventLinkRepo */
$eventLinkRepo = $container->get(EventLinkRepo::class);
$links = [];
foreach ($eventLinkRepo->findAll() as $link) {
    $links[$link->getId()] = ($phases[$link->getLinkPhaseIdFk()] ?? '') . ' – ' . $link->getHref();
}
/** @var EnrollRepoInterface $enrollRepo */
$enrollRepo = $container->get(EnrollRepo::class);
$enrolled = [];
foreach ($enrollRepo->findAll() as $enroll) {
    $enrolled[$enroll->getEventIdFk()] = ($enrolled[$enroll->getEventIdFk()] ?? 0) + 1;
}
// R1: obsazená místa rezervacemi škol (jen se zapnutým přepínačem vp.rezervace)
$reserved = \Application\Feature\FeatureFlags::fromConfiguration()->isEnabled('vp.rezervace')
    ? $container->get(\Events\Service\Reservation\SchoolReservationService::class)->occupiedByEvent() : [];
/** @var EventRepoInterface $eventRepo */
$eventRepo = $container->get(EventRepo::class);
$rows = [];
foreach ($eventRepo->findAll() as $event) {
    $rows[] = ['id' => $event->getId(), 'start' => $event->getStart(), 'end' => $event->getEnd(),
        'published' => $event->getPublished(), 'contentId' => $event->getEventContentIdFk(),
        'enrollLinkId' => $event->getEnrollLinkIdFk(), 'enterLinkId' => $event->getEnterLinkIdFk(),
        'enrolled' => $enrolled[$event->getId()] ?? 0, 'contents' => $contents, 'links' => $links,
        'capacity' => $event->getCapacity(), 'reserved' => $reserved[$event->getId()] ?? 0];
}
usort($rows, fn($a, $b) => ($a['start'] ? $a['start']->getTimestamp() : 0) <=> ($b['start'] ? $b['start']->getTimestamp() : 0));
$lastDate = $rows ? end($rows)['start']?->format('Y-m-d') : '';
?>
<div class="ui segment">
    <h3>Eventy (rozpis programu)</h3>
    <p>Datum vyberte v kalendáři, čas napište (např. 10:30). Na webu se zobrazí jen zveřejněné eventy.
        Event s přihlášenými nejde smazat – zrušte zveřejnění.
        Kapacita (počet míst pro žáky i pedagogy) je povinná u přednášek nabízených k rezervaci školám.</p>
    <?php if (!$contents): ?>
        <p class="ui orange message">Nejdřív založte obsahy eventů.</p>
    <?php endif; ?>
    <?= $this->repeat(__DIR__ . '/event.php', $rows) ?>
    <h4>Nový event</h4>
    <?= $this->insert(__DIR__ . '/event.php', ['contents' => $contents, 'links' => $links, 'defaultDate' => $lastDate]) ?>
</div>
