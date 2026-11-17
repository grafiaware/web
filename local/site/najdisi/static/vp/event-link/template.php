<?php
/**
 * G0 (#116): správa odkazů eventů (adresa online vstupu nebo přihlášky, fáze = text tlačítka, zobrazit ano/ne)
 * – jen events_administrator. Akce: EventControler (events/v1/eventlink).
 */
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Core\Text\Html;
use Component\ViewModel\StatusViewModel;
use Access\Enum\RoleEnum;
use Events\Model\Repository\EventLinkRepo;
use Events\Model\Repository\EventLinkRepoInterface;
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
$phases = [];
foreach ($eventLinkPhaseRepo->findAll() as $phase) {
    $phases[$phase->getId()] = $phase->getText();
}
/** @var EventLinkRepoInterface $eventLinkRepo */
$eventLinkRepo = $container->get(EventLinkRepo::class);
$rows = [];
foreach ($eventLinkRepo->findAll() as $link) {
    $rows[] = ['id' => $link->getId(), 'href' => $link->getHref(), 'show' => $link->getShow(),
        'linkPhaseId' => $link->getLinkPhaseIdFk(), 'phases' => $phases];
}
?>
<div class="ui segment">
    <h3>Odkazy eventů</h3>
    <p>Odkaz se k eventu přiřadí ve správě eventů (odkaz pro vstup u online eventu). Bez fází odkazů nejde odkaz uložit –
        nejdřív je založte ve správě fází.</p>
    <?= $this->repeat(__DIR__ . '/event-link.php', $rows) ?>
    <h4>Nový odkaz</h4>
    <?= $this->insert(__DIR__ . '/event-link.php', ['phases' => $phases]) ?>
</div>
