<?php
use Site\ConfigurationCache;
use Events\Middleware\Events\ViewModel\EventViewModel;

use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Red\Model\Entity\EnrollInterface;  // pro vdoc

//include 'data.php';

$headline = 'Můžete se těšit na tyto přednášky';
$perex =
    '
Další přednášky průběžně doplňujeme, koukněte sem za pár dnů!

Přednášky můžete i opakovaně zhlédnout na našem youtube kanálu. Odkaz na youtube kanál zde najdete po 28. 3. 2021';
$footer = 'Další přednášky budou postupně přibývat, sledujte tuto stránku!';


$eventTypeName = "";  // viz Events\Middleware\Events\ViewModel\EventType
$institutionName = "";

$event = [];
$eventList = new EventViewModel($statusSecurity);

$eventIds = [];
foreach ($enrolls as $enroll) {
    $eventIds[] = $enroll->getEventid();
}

$event = $eventList->getEventList(null, $institutionName, $eventIds, false);   // enrolling = false


//include Configuration::componentControler()['templates']."timecolumn/template.php";
//include Configuration::componentControler()['templates']."timeline-boxes/template.php";


?>
<div class="title">
    <i class="dropdown icon"></i>
    Můj harmonogram
</div>
<div class="content">
    <?php include ConfigurationCache::eventTemplates()['templates']."timeline-leafs/content/timeline.php"; ?>
</div>