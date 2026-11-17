<?php
/**
 * G0 (#116): správa institucí (pořadatelé a vystupující u obsahů eventů) – jen events_administrator.
 * Akce: EventControler (events/v1/institution).
 */
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Core\Text\Html;
use Component\ViewModel\StatusViewModel;
use Access\Enum\RoleEnum;
use Events\Model\Repository\InstitutionRepo;
use Events\Model\Repository\InstitutionRepoInterface;
use Events\Model\Repository\InstitutionTypeRepo;
use Events\Model\Repository\InstitutionTypeRepoInterface;

/** @var PhpTemplateRendererInterface $this */

/** @var StatusViewModel $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
if ($statusViewModel->getUserRole() != RoleEnum::EVENTS_ADMINISTRATOR) {
    echo Html::p("Stránka je určena pouze pro administraci programu.", ["class" => "ui orange segment"]);
    return;
}

/** @var InstitutionTypeRepoInterface $institutionTypeRepo */
$institutionTypeRepo = $container->get(InstitutionTypeRepo::class);
$types = [];
foreach ($institutionTypeRepo->findAll() as $institutionType) {
    $types[$institutionType->getId()] = $institutionType->getInstitutionType();
}
/** @var InstitutionRepoInterface $institutionRepo */
$institutionRepo = $container->get(InstitutionRepo::class);
$rows = [];
foreach ($institutionRepo->findAll() as $institution) {
    $rows[] = ['id' => $institution->getId(), 'name' => $institution->getName(),
        'institutionTypeId' => $institution->getInstitutionTypeId(), 'types' => $types];
}
usort($rows, fn($a, $b) => strcoll((string) $a['name'], (string) $b['name']));
?>
<div class="ui segment">
    <h3>Instituce</h3>
    <p>Pořadatelé a vystupující u obsahů eventů (firmy, úřad práce, školy…).</p>
    <?= $this->repeat(__DIR__ . '/institution.php', $rows) ?>
    <h4>Nová instituce</h4>
    <?= $this->insert(__DIR__ . '/institution.php', ['types' => $types]) ?>
</div>
