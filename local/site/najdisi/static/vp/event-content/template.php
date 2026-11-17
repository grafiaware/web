<?php
/**
 * G0 (#116): správa obsahů eventů (název, popis, vystupující, instituce, typ) – jen events_administrator.
 * Obsah se přiřadí eventu (čas) ve správě eventů. Akce: EventControler (events/v1/eventcontent).
 */
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Core\Text\Html;
use Component\ViewModel\StatusViewModel;
use Access\Enum\RoleEnum;
use Events\Model\Repository\EventContentRepo;
use Events\Model\Repository\EventContentRepoInterface;
use Events\Model\Repository\EventContentTypeRepo;
use Events\Model\Repository\EventContentTypeRepoInterface;
use Events\Model\Repository\InstitutionRepo;
use Events\Model\Repository\InstitutionRepoInterface;

/** @var PhpTemplateRendererInterface $this */

/** @var StatusViewModel $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
if ($statusViewModel->getUserRole() != RoleEnum::EVENTS_ADMINISTRATOR) {
    echo Html::p("Stránka je určena pouze pro administraci programu.", ["class" => "ui orange segment"]);
    return;
}

/** @var EventContentTypeRepoInterface $eventContentTypeRepo */
$eventContentTypeRepo = $container->get(EventContentTypeRepo::class);
$types = [];
foreach ($eventContentTypeRepo->findAll() as $contentType) {
    $types[$contentType->getId()] = $contentType->getName() ?: $contentType->getType();
}
/** @var InstitutionRepoInterface $institutionRepo */
$institutionRepo = $container->get(InstitutionRepo::class);
$institutions = [];
foreach ($institutionRepo->findAll() as $institution) {
    $institutions[$institution->getId()] = $institution->getName();
}
asort($institutions, SORT_LOCALE_STRING);
/** @var EventContentRepoInterface $eventContentRepo */
$eventContentRepo = $container->get(EventContentRepo::class);
$rows = [];
foreach ($eventContentRepo->findAll() as $content) {
    $rows[] = ['id' => $content->getId(), 'title' => $content->getTitle(), 'perex' => $content->getPerex(),
        'party' => $content->getParty(), 'selectedInstitutionId' => $content->getInstitutionIdFk(),
        'contentTypeId' => $content->getEventContentTypeIdFk(), 'types' => $types, 'institutions' => $institutions];
}
?>
<div class="ui segment">
    <h3>Obsahy eventů</h3>
    <p>Co se na eventu děje (přednáška, poradna…). Čas a zveřejnění se nastavují ve správě eventů.</p>
    <?php if (!$types): ?>
        <p class="ui orange message">Nejdřív založte typy obsahu.</p>
    <?php endif; ?>
    <?= $this->repeat(__DIR__ . '/event-content.php', $rows) ?>
    <h4>Nový obsah</h4>
    <?= $this->insert(__DIR__ . '/event-content.php', ['types' => $types, 'institutions' => $institutions]) ?>
</div>
