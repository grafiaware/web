<?php
use Site\Configuration;
use Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

$headline = 'Chci navázat kontakt';

$corporateContacts = [
    [
        'kontaktniOsoba' => 'Konplan',
        'funkce' => '',
        'telefon' => '377 918 109',
        'email' => 'kariera@konplan.cz',
    ]
];
$corporateAddress = [
    'pobockaFirmyUlice' => 'Technická 3017/1',
    'pobockaFirmyMesto' => '301 00 Plzeň-město',
    
]

?>

    <div id="chci-navazat-kontakt">
       <?php
       
        
        include Configuration::componentController()['templates']."presenter-contacts/template.php";
        ?>
    </div>