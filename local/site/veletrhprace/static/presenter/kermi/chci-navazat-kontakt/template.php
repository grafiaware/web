<?php
use Site\Configuration;
use Events\Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;

$headline = 'Chci navázat kontakt';

$corporateContacts = [
    [
        'kontaktniOsoba' => 'Romana Presslová',
        'funkce' => 'personální péče a nábor',
        'telefon' => '374 611 275',
        'email' => 'Presslova.Romana@kermi.cz',
    ]
];
$corporateAddress = [
    'pobockaFirmyUlice' => 'Dukelská 1427',
    'pobockaFirmyMesto' => '349 01 Stříbro',
    
]

?>

    <div id="chci-navazat-kontakt">
       <?php
       
        
        include Configuration::componentController()['templates']."paper/presenter-contacts.php";
        ?>
    </div>