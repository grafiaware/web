<?php
use Site\Configuration;
use Model\Arraymodel\EventList;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

$headline = 'Chci navázat kontakt';

$corporateContacts = [
    [
        'kontaktniOsoba' => 'Barbora Krejčová',
        'funkce' => 'HR Specialista',
        'telefon' => '383 826 440',
        'email' => 'barbora.krejcova@wienerberger.com',
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