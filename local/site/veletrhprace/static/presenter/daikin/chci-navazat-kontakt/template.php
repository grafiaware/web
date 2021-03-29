<?php
use Site\Configuration;
use Model\Arraymodel\EventList;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

$headline = 'Chci navázat kontakt';

$corporateContacts = [
    [
        'kontaktniOsoba' => 'Daikin',
        'funkce' => '',
        'telefon' => '378 773 111',
        'email' => 'kariera@daikinczech.cz',
    ]
];
$corporateAddress = [
    'pobockaFirmyUlice' => 'U Nové Hospody 1',
    'pobockaFirmyMesto' => '301 00 Plzeň',
    
]

?>

    <div id="chci-navazat-kontakt">
       <?php
       
        
        include Configuration::componentControler()['templates']."presenter-contacts/template.php";
        ?>
    </div>