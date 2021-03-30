<?php
use Site\Configuration;
use Model\Arraymodel\EventList;

use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Model\Entity\EnrollInterface;  // pro vdoc

//include 'data.php';

$headline = 'Můžete se těšit na tyto přednášky';
$perex =
    '
Další přednášky průběžně doplňujeme, koukněte sem za pár dnů!

Přednášky můžete i opakovaně zhlédnout na našem youtube kanálu. Odkaz na youtube kanál zde najdete po 28. 3. 2021';
$footer = 'Další přednášky budou postupně přibývat, sledujte tuto stránku!';


$company = [
  1 => ["name" => "Wienerberger s.r.o."],
  2 => ["name" =>"Daikin Industries Czech Republic s.r.o."],
  3 => ["name" => "AKKA Czech Republic s.r.o."],
  4 => ["name" => "MD ELEKTRONIK spol. s r.o."],
  5 => ["name" => ""],

];

$presenter =
[
    'Krejčová' => [$name => "Krejčová", $mail => "barbora.krejcova@wienerberger.com", $company => "Wienerberger s.r.o."],
    'Tomáš Matoušek' => [$name => "Tomáš Matoušek", $mail => "matousek.t@daikinczech.cz", $company => "Daikin Industries Czech Republic s.r.o."],
    'Elizabeth Franková' => [$name => "Elizabeth Franková", $mail => "Elizabeth.frankova@akka.eu", $company => "Akka Czech Republice s.r.o."],
    'KaterinaJanku' => [$name => "KaterinaJanku", $mail => "katerina.janku@akka.eu", $company => "AKKA Czech Republic"],
    'Šárka Bilíková' => [$name => "Šárka Bilíková", Slunicko2021       $mail => "sarka.bilikova@akka.eu", $company => "AKKA Czech Republic s.r.o."],
    'VERONIKA' => [$name => "VERONIKA", $mail => "Veronika.Simbartlova@md-elektronik.cz", $company => "MD ELEKTRONIK sro"],
    'Kristýna Křížová' => [$name => "Kristýna Křížová", $mail => "kristyna.krizova@md-elektronik.cz", $company => "MD ELEKTRONIK spol. s r.o."],
];

$eventTypeName = "";  // viz Model\Arraymodel\EventType
$institutionName = "";

$event = [];
$eventList = new EventList($statusSecurity);

foreach ($enrolls as $enroll) {
    $eventIds[] = $enroll->getEventid();
}

$event = $eventList->getEventList(null, null, $eventIds, false);   // enrolling = false


//include Configuration::componentControler()['templates']."timecolumn/template.php";
//include Configuration::componentControler()['templates']."timeline-boxes/template.php";


?>
<div class="title">
    <i class="dropdown icon"></i>
    Můj harmonogram
</div>
<div class="content">
    <?php include Configuration::componentControler()['templates']."timeline-leafs/content/timeline.php"; ?>
</div>