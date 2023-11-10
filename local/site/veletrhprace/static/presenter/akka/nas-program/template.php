<?php
use Site\ConfigurationCache;
use Events\Middleware\Events\ViewModel\EventViewModel;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Status\Model\Repository\StatusSecurityRepo;

    $statusSecurityRepo = $container->get(StatusSecurityRepo::class);
    /** @var StatusSecurityRepo $statusSecurityRepo */
    $statusSecurity = $statusSecurityRepo->get();

//    $eventTypeName = "Prezentace, Pohovor, Přednáška";  // viz Events\Middleware\Events\ViewModel\EventType
    $institutionName = "AKKA Czech Republic";
    $event = (new EventViewModel($statusSecurity))->getEventList("", $institutionName, [], true);   // enrolling = true



    $headline = 'Náš program';
    $perex =
        '
        <p class="text ">I když online veletrh s živou účastí skončil, stále zde naleznete přístupy k záznamům přednášek, které můžete zhlédnout. </p>
        ';
    $footer = '';
?>


    <div id="nas-program">
        <?php
        //include Configuration::componentController()['templates']."timecolumn/template.php";
        include ConfigurationCache::eventTemplates()['templates']."paper/timeline-boxes.php";
        ?>
    </div>
