<?php
use Site\Configuration;
use Model\Arraymodel\EventList;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Model\Repository\StatusSecurityRepo;

    $statusSecurityRepo = $container->get(StatusSecurityRepo::class);
    /** @var StatusSecurityRepo $statusSecurityRepo */
    $statusSecurity = $statusSecurityRepo->get();
    $eventTypeName = "";  // viz Model\Arraymodel\EventType
    $institutionName = "EURES";
    $event = (new EventList($statusSecurity))->getEventList($eventTypeName, $institutionName, [], true);   // enrolling = true



    $headline = 'Náš program';
    $perex =
        '
        ';
    $footer = '';
?>


    <div id="nas-program">
        <p>Podívejte se také na naše videonávody.</p>
        <?php
        //include Configuration::componentControler()['templates']."timecolumn/template.php";
        include Configuration::componentControler()['templates']."timeline-boxes/template.php";
        ?>
    </div>