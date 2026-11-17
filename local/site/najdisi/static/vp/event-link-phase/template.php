<?php
/**
 * G0 (#116): správa fází odkazů eventu (text tlačítka, např. „Přihlásit se“, „Vstoupit“, „Zhlédnout záznam“)
 * – jen events_administrator. Akce: EventControler (events/v1/eventlinkphase).
 */
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Core\Text\Html;
use Component\ViewModel\StatusViewModel;
use Access\Enum\RoleEnum;
use Events\Model\Repository\EventLinkPhaseRepo;
use Events\Model\Repository\EventLinkPhaseRepoInterface;

/** @var PhpTemplateRendererInterface $this */

/** @var StatusViewModel $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
if ($statusViewModel->getUserRole() != RoleEnum::EVENTS_ADMINISTRATOR) {
    echo Html::p("Stránka je určena pouze pro administraci programu.", ["class" => "ui orange segment"]);
    return;
}

/** @var EventLinkPhaseRepoInterface $eventLinkPhaseRepo */
$eventLinkPhaseRepo = $container->get(EventLinkPhaseRepo::class);
$rows = [];
foreach ($eventLinkPhaseRepo->findAll() as $phase) {
    $rows[] = ['id' => $phase->getId(), 'text' => $phase->getText()];
}
?>
<div class="ui segment">
    <h3>Fáze odkazů eventu</h3>
    <p>Text tlačítka u odkazu eventu (např. „Vstoupit“, „Zhlédnout záznam“).</p>
    <?= $this->repeat(__DIR__ . '/event-link-phase.php', $rows) ?>
    <h4>Nová fáze</h4>
    <?= $this->insert(__DIR__ . '/event-link-phase.php', []) ?>
</div>
