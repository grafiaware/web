<?php
use Site\ConfigurationCache;
use Events\Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;

use Status\Model\Repository\StatusSecurityRepo;

    $statusSecurityRepo = $container->get(StatusSecurityRepo::class);
    /** @var StatusSecurityRepo $statusSecurityRepo */
    $statusSecurity = $statusSecurityRepo->get();
    $eventTypeName = "Poradna";  // viz Events\Model\Arraymodel\EventType
    $institutionName = "Grafia";
    $event = (new Event($statusSecurity))->getEventList($eventTypeName, $institutionName, [], true);   // enrolling = true

    
    
        $headline = 'Naše poradny';
        $perex =
            '
                  <p class="text ">Možnost online pohovorů se zástupci firem již skončila. Využijte kontaktní údaje firmy níže nebo rovnou vložte své pracovní údaje a životopis z profilu návštěvníka.</p>
            
            ';
        $footer = '';
?>

    <div id="chci-na-online-pohovor">
        <?php
        //include Configuration::componentController()['templates']."timecolumn/template.php";
        include ConfigurationCache::componentController()['templates']."paper/timeline-boxes.php";
        ?>
    </div>