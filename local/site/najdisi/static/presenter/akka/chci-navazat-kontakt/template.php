<?php
use Site\ConfigurationCache;
use Events\Middleware\Events\ViewModel\EventViewModel;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;

$headline = 'Chci navázat kontakt';

$corporateContacts = [
    [
        'kontaktniOsoba' => 'Akka',
        'funkce' => 'personální oddělení',
        'telefon' => ' - ',
        'email' => 'personalcz@akka.eu',
    ]
];
$corporateAddress = [
    'pobockaFirmyUlice' => 'Daimlerova 1161/6',
    'pobockaFirmyMesto' => '301 00 Plzeň – Skvrňany',
    
]

?>

    <div id="chci-navazat-kontakt">
       <?php
       
        
        include ConfigurationCache::eventTemplates()['templates']."presenter-contacts.php";
        ?>
    </div>