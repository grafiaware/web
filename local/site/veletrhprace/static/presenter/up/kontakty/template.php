<?php
use Site\Configuration;
use Events\Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;

$headline = 'Chci navázat kontakt';

$corporateContacts = [
    [
        'kontaktniOsoba' => 'Úřad práce',
        'funkce' => 'pobočka Plzeň - město',
        'telefon' => '950 148 111',
        'email' => 'podatelna.pm@uradprace.cz',
    ]
];
$corporateAddress = [
    'pobockaFirmyUlice' => 'Kaplířova 2731/7',
    'pobockaFirmyMesto' => '305 88 Plzeň 1 - Jižní Předměstí',
    
]

?>

    <div id="chci-navazat-kontakt">
       <?php
       
        
        include Configuration::componentController()['templates']."presenter-contacts/template.php";
        ?>
        <p class="text nastred"><a href="https://www.uradprace.cz/plzen" target="_blank">Všechny kontaktní údaje</a></p>
    </div>