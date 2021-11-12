<?php
use Site\Configuration;
use Events\Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;

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
       
        
        include Configuration::componentController()['templates']."paper/presenter-contacts.php";
        ?>
    </div>