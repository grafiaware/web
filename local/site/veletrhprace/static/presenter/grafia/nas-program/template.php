<?php
use Site\Configuration;
use Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Module\Status\Model\Repository\StatusSecurityRepo;

    $statusSecurityRepo = $container->get(StatusSecurityRepo::class);
    /** @var StatusSecurityRepo $statusSecurityRepo */
    $statusSecurity = $statusSecurityRepo->get();

    $institutionName = "Grafia";
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
        include Configuration::componentController()['templates']."timeline-boxes/template.php";
        ?>
    </div>
