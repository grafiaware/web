<?php
use Site\ConfigurationCache;
use Events\Middleware\Events\ViewModel\EventViewModel;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Status\Model\Repository\StatusSecurityRepo;

    $statusSecurityRepo = $container->get(StatusSecurityRepo::class);
    /** @var StatusSecurityRepo $statusSecurityRepo */
    $statusSecurity = $statusSecurityRepo->get();
    $eventTypeName = "";  // viz Events\Middleware\Events\ViewModel\EventType
    $institutionName = "EURES";
    $event = (new EventViewModel($statusSecurity))->getEventList($eventTypeName, $institutionName, [], true);   // enrolling = true



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
        include ConfigurationCache::eventTemplates()['templates']."timeline-boxes.php";
        ?>
        
    </div>
