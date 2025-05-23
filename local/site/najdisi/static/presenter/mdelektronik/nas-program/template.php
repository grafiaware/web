<?php
use Site\ConfigurationCache;
use Events\Middleware\Events\ViewModel\EventViewModel;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Status\Model\Repository\StatusSecurityRepo;

    $statusSecurityRepo = $container->get(StatusSecurityRepo::class);
    /** @var StatusSecurityRepo $statusSecurityRepo */
    $statusSecurity = $statusSecurityRepo->get();

    $institutionName = "MD Elektronik";
    $event = (new EventViewModel($statusSecurity))->getEventList('', $institutionName, [], true);   // enrolling = true



    $headline = 'Náš program';
    $perex =
        '
           <p class="text">Online veletrh s živou účastí již skončil. Využijte kontaktní údaje firmy níže</p>
        ';
    $footer = '';
?>


    <div id="nas-program">
        <?php
        //include Configuration::componentControler()['templates']."timecolumn/template.php";
        include ConfigurationCache::eventTemplates()['templates']."timeline-boxes.php";
        ?>
    </div>
