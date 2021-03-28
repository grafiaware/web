<?php
use Site\Configuration;
use Model\Arraymodel\EventList;

use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */


$headline = 'Poradny online zdarma';
$perex =
    '
Přihlaste se na konkrétní čas v poradnách! (po 23. 3. 2021)
';
$footer = '';

$eventTypeName = "Poradna";  // viz Model\Arraymodel\EventType
$institutionName = "";

$event = (new EventList())->getEventList($eventTypeName, $institutionName);

//include Configuration::componentControler()['templates']."timecolumn/template.php";
include Configuration::componentControler()['templates']."timeline-boxes/template.php";
//include Configuration::componentControler()['templates']."timeline-leafs/template.php";

?>
