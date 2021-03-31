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

Přednášky můžete i opakovaně zhlédnout na našem <a href="https://www.youtube.com/channel/UC-Di-88rpUfBZUHHVf7tntQ" target="_blank">youtube kanálu</a>.';
$footer = '';

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
