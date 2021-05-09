<?php
use Site\Configuration;
use Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Status\Model\Repository\StatusSecurityRepo;

$headline = 'Poradny online zdarma';
$perex =
    '

';
$perex1 =
    '
Přihlaste se do vybrané poradny! Vstoupit mohou pouze registrovaní návštěvníci. Přihlašovací tlačítko u každé poradny uvidíte teprve po registraci či přihlášení do vašeho účtu na tomto webu. 
';
$footer = '';

$eventTypeName = "Poradna";  // viz Model\Arraymodel\EventType
$institutionName = "";

$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurity = $statusSecurityRepo->get();

$event = (new Event($statusSecurity))->getEventList($eventTypeName, $institutionName, [], true);   // enrolling = true

//include Configuration::componentController()['templates']."timecolumn/template.php";
include Configuration::componentController()['templates']."timeline-boxes/template.php";
//include Configuration::componentController()['templates']."timeline-leafs/template.php";

?>
