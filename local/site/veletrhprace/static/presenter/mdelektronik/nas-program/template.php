<?php
use Site\Configuration;
use Events\Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Status\Model\Repository\StatusSecurityRepo;

    $statusSecurityRepo = $container->get(StatusSecurityRepo::class);
    /** @var StatusSecurityRepo $statusSecurityRepo */
    $statusSecurity = $statusSecurityRepo->get();

    $institutionName = "MD Elektronik";
    $event = (new Event($statusSecurity))->getEventList('', $institutionName, [], true);   // enrolling = true



    $headline = 'Náš program';
    $perex =
        '
           <p class="text">Online veletrh s živou účastí již skončil. Využijte kontaktní údaje firmy níže</p>
        ';
    $footer = '';
?>


    <div id="nas-program">
        <?php
        //include Configuration::componentController()['templates']."timecolumn/template.php";
        include Configuration::componentController()['templates']."paper/timeline-boxes.php";
        ?>
    </div>
