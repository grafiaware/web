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
        ' <p>I když online veletrh s živou účastí skončil, stále zde naleznete přístupy k záznamům přednášek, které můžete zhlédnout.</p> 
            <p>Podívejte se také na užitečné videonávody: 
            <a href="https://www.uradprace.cz/web/cz/zamestnanost-videa" target="_blank">zaměstnanost</a> 
            a <a href="https://www.uradprace.cz/web/cz/-/rekvalifik-14" target="_blank">zvolená rekvalifikace</a></p>
        ';
    $footer = '';
?>


    <div id="nas-program">
        <?php
        //include Configuration::componentControler()['templates']."timecolumn/template.php";
        include Configuration::componentControler()['templates']."timeline-boxes/template.php";
        ?>
        
    </div>
