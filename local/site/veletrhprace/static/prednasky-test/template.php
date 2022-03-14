<?php
use Site\Configuration;
use Events\Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Status\Model\Repository\StatusSecurityRepo;


//include 'data.php';

$headline = 'Můžete se těšit na tyto přednášky';
$perex =
    '

Přednášky můžete i opakovaně zhlédnout na našem <a href="https://www.youtube.com/channel/UC-Di-88rpUfBZUHHVf7tntQ" target="_blank">youtube kanálu</a>.
';
$perex1 =
    '

Přednášky můžete i opakovaně zhlédnout na našem <a href="https://www.youtube.com/channel/UC-Di-88rpUfBZUHHVf7tntQ" target="_blank">youtube kanálu</a>.
Přihlaste se na náš program! Přihlašovací tlačítko uvidíte teprve po registraci či přihlášení do vašeho účtu na tomto webu.
Před zahájením akce zde uvidíte barevný odkaz pro vstup na akci nebo odkaz pro zhlédnutí vybraného videa. '
        ;
$footer = '';

$eventTypeName = "Přednáška";  // viz Events\Model\Arraymodel\EventType
$institutionName = "";


$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurity = $statusSecurityRepo->get();

$event = (new Event($statusSecurity))->getEventList($eventTypeName, $institutionName, [], true);   // enrolling = true

//include Configuration::componentController()['templates']."paper/timecolumn.php";
include Configuration::componentController()['templates']."paper/timeline-boxes.php";
//include Configuration::componentController()['templates']."paper/timeline-leafs.php";


?>
