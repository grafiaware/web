<?php
use Site\ConfigurationCache;
use Events\Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Status\Model\Repository\StatusSecurityRepo;

    $statusSecurityRepo = $container->get(StatusSecurityRepo::class);
    /** @var StatusSecurityRepo $statusSecurityRepo */
    $statusSecurity = $statusSecurityRepo->get();

    $institutionName = "Wienerberger";
    $event = (new Event($statusSecurity))->getEventList('', $institutionName, [], true);   // enrolling = true



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
        include ConfigurationCache::componentController()['templates']."paper/timeline-boxes.php";
        ?>
    </div>
