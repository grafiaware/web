<?php
use Site\Configuration;
use Model\Arraymodel\EventList;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

$headline = 'Chci navázat kontakt';

$corporateContacts = [
    [
        'kontaktniOsoba' => 'Wienerberger',
        'funkce' => 'centrála',
        'telefon' => '+420 727 326 111',
        'email' => 'info@wienerberger.cz',
    ]
];
$corporateAddress = [
    'pobockaFirmyUlice' => 'Plachého 388/28',
    'pobockaFirmyMesto' => '370 01  České Budějovice 1',
    
]

?>

    <div id="chci-navazat-kontakt">
       <?php
       
        
        include Configuration::componentControler()['templates']."presenter-contacts/template.php";
        ?>
    </div>