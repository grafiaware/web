<?php
use Site\Configuration;
use Model\Arraymodel\EventList;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

use Model\Repository\StatusSecurityRepo;

    $statusSecurityRepo = $container->get(StatusSecurityRepo::class);
    /** @var StatusSecurityRepo $statusSecurityRepo */
    $statusSecurity = $statusSecurityRepo->get();
    $eventTypeName = "Poradna";  // viz Model\Arraymodel\EventType
    $institutionName = "Grafia";
    $event = (new EventList($statusSecurity))->getEventList($eventTypeName, $institutionName, [], true);   // enrolling = true

    
    
        $headline = 'Naše poradny';
        $perex =
            '
                <p class="text ">Vstoupit mohou pouze registrovaní návštěvníci. Přihlašovací tlačítko uvidíte teprve po registraci či přihlášení do vašeho účtu na tomto webu. 
            Před zahájením akce zde uvidíte barevný odkaz pro vstup na akci nebo odkaz pro zhlédnutí vybraného videa. </p>
            ';
        $footer = '';
?>

    <div id="chci-na-online-pohovor">
        <?php
        //include Configuration::componentControler()['templates']."timecolumn/template.php";
        include Configuration::componentControler()['templates']."timeline-boxes/template.php";
        ?>
    </div>