<?php
use Site\Configuration;
use Model\Arraymodel\EventList;

use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Model\Repository\StatusSecurityRepo;


//include 'data.php';

$headline = 'Můžete se těšit na tyto přednášky';
$perex =
    '
Další přednášky průběžně doplňujeme, koukněte sem za pár dnů!

Přednášky můžete i opakovaně zhlédnout na našem youtube kanálu. Odkaz na youtube kanál zde najdete po 28. 3. 2021';
$footer = 'Další přednášky budou postupně přibývat, sledujte tuto stránku!';


$eventTypeName = "Přednáška";  // viz Model\Arraymodel\EventType
$institutionName = "";


$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurity = $statusSecurityRepo->get();

$event = (new EventList($statusSecurity))->getEventList($eventTypeName, $institutionName, [], true);   // enrolling = true

//include Configuration::componentControler()['templates']."timecolumn/template.php";
include Configuration::componentControler()['templates']."timeline-boxes/template.php";
//include Configuration::componentControler()['templates']."timeline-leafs/template.php";


?>
