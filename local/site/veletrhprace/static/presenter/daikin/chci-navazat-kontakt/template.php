<?php
use Site\ConfigurationCache;
use Events\Middleware\Events\ViewModel\EventViewModel;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;

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
       
        
        include ConfigurationCache::eventTemplates()['templates']."paper/presenter-contacts.php";
        ?>
    </div>