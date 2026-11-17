<?php
/**
 * G0 (#116): správa typů obsahu eventů (přednáška, poradna…) – jen events_administrator.
 * Akce: EventControler (events/v1/eventcontenttype).
 */
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Core\Text\Html;
use Component\ViewModel\StatusViewModel;
use Access\Enum\RoleEnum;
use Events\Model\Repository\EventContentTypeRepo;
use Events\Model\Repository\EventContentTypeRepoInterface;

/** @var PhpTemplateRendererInterface $this */

/** @var StatusViewModel $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
if ($statusViewModel->getUserRole() != RoleEnum::EVENTS_ADMINISTRATOR) {
    echo Html::p("Stránka je určena pouze pro administraci programu.", ["class" => "ui orange segment"]);
    return;
}

/** @var EventContentTypeRepoInterface $eventContentTypeRepo */
$eventContentTypeRepo = $container->get(EventContentTypeRepo::class);
$rows = [];
foreach ($eventContentTypeRepo->findAll() as $contentType) {
    $rows[] = ['id' => $contentType->getId(), 'type' => $contentType->getType(), 'name' => $contentType->getName()];
}
?>
<div class="ui segment">
    <h3>Typy obsahu eventů</h3>
    <p>Kód typu (např. <code>prednaska</code>, <code>poradna</code>) a název, který se zobrazí v programu.</p>
    <?= $this->repeat(__DIR__ . '/event-content-type.php', $rows) ?>
    <h4>Nový typ</h4>
    <?= $this->insert(__DIR__ . '/event-content-type.php', []) ?>
</div>
