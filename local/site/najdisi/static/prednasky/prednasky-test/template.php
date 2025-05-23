<?php
use Site\ConfigurationCache;
use Events\Middleware\Events\ViewModel\EventViewModel;

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

$eventTypeName = "Přednáška";  // viz Events\Middleware\Events\ViewModel\EventType
$institutionName = "";


$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurity = $statusSecurityRepo->get();

$event = (new EventViewModel($statusSecurity))->getEventList($eventTypeName, $institutionName, [], true);   // enrolling = true

//include Configuration::componentControler()['templates']."timecolumn.php";
include ConfigurationCache::eventTemplates()['templates']."timeline-boxes.php";
//include Configuration::componentControler()['templates']."timeline-leafs.php";


?>
