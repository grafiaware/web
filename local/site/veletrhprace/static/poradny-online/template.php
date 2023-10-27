<?php
use Site\ConfigurationCache;
use Psr\Container\ContainerInterface;
use Events\Middleware\Events\ViewModel\EventViewModel;

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

$eventTypeName = "Poradna";  // viz Events\Middleware\Events\ViewModel\EventType
$institutionName = "";

/** @var ContainerInterface $container */
/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
$statusSecurity = $statusSecurityRepo->get();

$event = (new EventViewModel($statusSecurity))->getEventList($eventTypeName, $institutionName, [], true);   // enrolling = true

//include Configuration::componentController()['templates']."paper/timecolumn.php";
include ConfigurationCache::componentController()['templates']."paper/timeline-boxes.php";
//include Configuration::componentController()['templates']."paper/timeline-leafs.php";

?>
