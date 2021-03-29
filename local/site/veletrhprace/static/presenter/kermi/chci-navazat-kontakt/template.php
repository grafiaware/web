<?php
use Site\Configuration;
use Model\Arraymodel\EventList;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

$headline = 'Chci navázat kontakt';

$corporateContacts = [
    [
        'kontaktniOsoba' => 'Radka Novotná',
        'funkce' => 'asistentka',
        'telefon' => '+420 758 659 855',
        'email' => 'firma@firmovata.cz',
    ]
];
$corporateAddress = [
    'pobockaFirmyUlice' => 'U velkého poníka 417',
    'pobockaFirmyMesto' => '800 45 Poníkov',
    
]

?>

    <div id="chci-navazat-kontakt">
       <?php
       
        
        include Configuration::componentControler()['templates']."presenter-contacts/template.php";
        ?>
    </div>