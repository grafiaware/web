<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

$headline = 'Můžete se těšit na tyto přednášky';
$perex =
    '
Další přednášky průběžně doplňujeme, koukněte sem za pár dnů!

Přednášky můžete i opakovaně zhlédnout na našem youtube kanálu. Odkaz na youtube kanál zde najdete po 28. 3. 2021';
$footer = 'Další přednášky budou postupně přibývat, sledujte tuto stránku!';


$linkButtonAttributes = ['class' => 'ui large red button'];
$linkButtonTextPrihlasitSe = 'Zde se budete moci přihlásit';
$linkButtonTextZhlednout = 'Zhlédnout záznam';

$eventType = [
    'Přednáška' => ['name'=>'Přednáška', 'icon'=>'chalkboard teacher icon'],
    'Pohovor'=> ['name'=>'Pohovor', 'icon'=> 'microphone icon'],
    'Poradna' => ['name'=>'Poradna', 'icon'=> 'user friends icon'],
];


$timelinePoint = [
        1 => '30. 3. 2021',
        2 => '31. 3. 2021',
        3 => '1. 4. 2021',
];

include 'data-event-content.php';

include 'data-event-list.php';


    $compareByStartTime = function ($boxA, $boxB) {
        return (str_replace(':', '', $boxA['startTime']) < str_replace(':', '', $boxB['startTime'])) ? -1 : 1;
    };

//    $eventTypeName = "Přednáška";
//    $eventTypeName = 'Pohovor';
//    $eventTypeName = "Poradna";

//    $institutionName = "Ledovec";
    $event = [];
    foreach ($timelinePoint as $tlPoint) {
        $boxItems = [];
        foreach ($eventList as $boxItem) {
            if (
                (isset($eventTypeName) ? $boxItem['eventType']['name']==$eventTypeName : true)
                    AND
                (isset($institutionName) ? $boxItem['institution']['name']==$institutionName : true)
                    AND
                    $boxItem['timelinePoint']==$tlPoint
                ) {
                $boxItems[] = $boxItem;
            }
        }

        uasort($boxItems, $compareByStartTime);

        $event[] = [
                    'timelinePoint' => $tlPoint,
                    'box' => $boxItems
                ];
    }
