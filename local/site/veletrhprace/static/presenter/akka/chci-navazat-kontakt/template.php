<?php
use Site\Configuration;
use Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

$headline = 'Chci navázat kontakt';

$corporateContacts = [
    [
        'kontaktniOsoba' => 'Akka',
        'funkce' => 'personální oddělení',
        'telefon' => ' - ',
        'email' => 'personalcz@akka.eu',
    ]
];
$corporateAddress = [
    'pobockaFirmyUlice' => 'Daimlerova 1161/6',
    'pobockaFirmyMesto' => '301 00 Plzeň – Skvrňany',
    
]

?>

    <div id="chci-navazat-kontakt">
       <?php
       
        
        include Configuration::componentController()['templates']."presenter-contacts/template.php";
        ?>
    </div>