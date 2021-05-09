<?php
use Site\Configuration;
use Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

use Status\Model\Repository\StatusSecurityRepo;

    $statusSecurityRepo = $container->get(StatusSecurityRepo::class);
    /** @var StatusSecurityRepo $statusSecurityRepo */
    $statusSecurity = $statusSecurityRepo->get();
    $eventTypeName = "Pohovor";  // viz Model\Arraymodel\EventType
    $institutionName = "Valeo Autoklimatizace";
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
        include Configuration::componentController()['templates']."timeline-boxes/template.php";
        ?>
    </div>