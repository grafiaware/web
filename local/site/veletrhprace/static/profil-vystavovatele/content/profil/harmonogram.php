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

$eventList = new EventViewModel($statusSecurity);
$eventInstitutionName = $representativeContext["nameCompany"];
$event = $eventList->getEventList(null, $eventInstitutionName, [], false);   // enrolling = false
?>

<div class="title">
    <i class="dropdown icon"></i>
    Harmonogram
</div>
<div class="content">
    <?php include ConfigurationCache::eventTemplates()['templates']."paper/timeline-leafs/content/timeline.php";
//include Configuration::componentController()['templates']."timecolumn/template.php";
//include Configuration::componentController()['templates']."timeline-boxes/template.php";
    ?>
</div>