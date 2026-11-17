<?php
/**
 * G0 (#116): správa typů institucí (firma, úřad práce, škola…) – jen events_administrator.
 * Akce: EventControler (events/v1/institutiontype).
 */
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Core\Text\Html;
use Component\ViewModel\StatusViewModel;
use Access\Enum\RoleEnum;
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
$rows = [];
foreach ($institutionTypeRepo->findAll() as $institutionType) {
    $rows[] = ['id' => $institutionType->getId(), 'institutionType' => $institutionType->getInstitutionType()];
}
?>
<div class="ui segment">
    <h3>Typy institucí</h3>
    <?= $this->repeat(__DIR__ . '/institution-type.php', $rows) ?>
    <h4>Nový typ</h4>
    <?= $this->insert(__DIR__ . '/institution-type.php', []) ?>
</div>
