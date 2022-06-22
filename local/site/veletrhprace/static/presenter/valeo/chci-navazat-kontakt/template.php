<?php
use Site\ConfigurationCache;
use Events\Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;

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
       
        
        include ConfigurationCache::componentController()['templates']."paper/presenter-contacts.php";
        ?>
    </div>