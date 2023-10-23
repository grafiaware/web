<?php
use Site\ConfigurationCache;
use Events\Model\Arraymodel\EventViewModel;

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


$eventTypeName = "";  // viz Events\Model\Arraymodel\EventType
$eventInstitutionName = "";
$event = [];
$eventList = new EventViewModel($statusSecurity);


$eventInstitutionName = $presenterPerson["nameCompany"];

$event = $eventList->getEventList(null, $eventInstitutionName, [], false);   // enrolling = false


//include Configuration::componentController()['templates']."timecolumn/template.php";
//include Configuration::componentController()['templates']."timeline-boxes/template.php";


?>
<div class="title">
    <i class="dropdown icon"></i>
    Harmonogram
</div>
<div class="content">
    <?php include ConfigurationCache::componentController()['templates']."paper/timeline-leafs/content/timeline.php"; ?>
</div>