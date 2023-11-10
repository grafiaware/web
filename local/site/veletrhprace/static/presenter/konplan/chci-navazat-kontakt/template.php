<?php
use Site\ConfigurationCache;
use Events\Middleware\Events\ViewModel\EventViewModel;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;

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
       
        
        include ConfigurationCache::eventTemplates()['templates']."presenter-contacts.php";
        ?>
    </div>