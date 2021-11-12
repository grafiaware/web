<?php
use Site\Configuration;
use Events\Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;

use Status\Model\Repository\StatusSecurityRepo;

    $statusSecurityRepo = $container->get(StatusSecurityRepo::class);
    /** @var StatusSecurityRepo $statusSecurityRepo */
    $statusSecurity = $statusSecurityRepo->get();
    $eventTypeName = "Pohovor";  // viz Events\Model\Arraymodel\EventType
    $institutionName = "Daikin";
    $event = (new Event($statusSecurity))->getEventList($eventTypeName, $institutionName, [], true);   // enrolling = true

    
    
        $headline = 'Online pohovor';
        $perex =
            '
            ';
        $footer = '';
?>

    <div id="chci-na-online-pohovor">
        <?php
        //include Configuration::componentController()['templates']."timecolumn/template.php";
        include Configuration::componentController()['templates']."paper/timeline-boxes.php";
        ?>
    </div>