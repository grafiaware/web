<?php
use Site\ConfigurationCache;
use Events\Middleware\Events\ViewModel\EventViewModel;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;

use Status\Model\Repository\StatusSecurityRepo;

    $statusSecurityRepo = $container->get(StatusSecurityRepo::class);
    /** @var StatusSecurityRepo $statusSecurityRepo */
    $statusSecurity = $statusSecurityRepo->get();
    $eventTypeName = "Poradna";  // viz Events\Middleware\Events\ViewModel\EventType
    $institutionName = "Grafia";
    $event = (new EventViewModel($statusSecurity))->getEventList($eventTypeName, $institutionName, [], true);   // enrolling = true

    
    
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
        include ConfigurationCache::eventTemplates()['templates']."timeline-boxes.php";
        ?>
    </div>