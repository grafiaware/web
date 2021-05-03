<?php
use Site\Configuration;
use Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

$headline = 'Chci navázat kontakt';

$corporateContacts = [
    [
        'kontaktniOsoba' => 'Drůběžářský závod Klatovy',
        'funkce' => '',
        'telefon' => '376 353 382, 376 353 332',
        'email' => 'personalni@dzklatovy.cz',
    ]
];
$corporateAddress = [
    'pobockaFirmyUlice' => '5. května 112',
    'pobockaFirmyMesto' => '33901 Klatovy',
    
]
?>

    <div id="chci-navazat-kontakt">
       <?php
       
        
        include Configuration::componentController()['templates']."presenter-contacts/template.php";
        ?>
    </div>