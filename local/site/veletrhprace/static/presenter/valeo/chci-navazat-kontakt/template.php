<?php
use Site\Configuration;
use Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

$headline = 'Chci navázat kontakt';

$corporateContacts = [
    [
        'kontaktniOsoba' => 'Martina Pochová',
        'funkce' => '',
        'telefon' => '319 800 113',
        'email' => 'martina.pochova@valeo.com',
    ]
];
$corporateAddress = [
    'pobockaFirmyUlice' => 'Sazečská 247/2',
    'pobockaFirmyMesto' => '108 00 Praha',
    
]

?>

    <div id="chci-navazat-kontakt">
       <?php
       
        
        include Configuration::componentController()['templates']."presenter-contacts/template.php";
        ?>
    </div>