<?php
use Site\ConfigurationCache;
use Events\Middleware\Events\ViewModel\EventViewModel;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;

$headline = 'Chci navázat kontakt';

$corporateContacts = [
    [
        'kontaktniOsoba' => 'Grafia',
        'funkce' => 'sekretariát',
        'telefon' => '+420 377 227 701',
        'email' => 'sekretariat@grafia.cz',
    ],
    [
        'kontaktniOsoba' => 'Grafia',
        'funkce' => 'rekvalifikace',
        'telefon' => '+420 378 771 222',
        'email' => 'rekvalifikace@grafia.cz',
    ]
]; 
$corporateAddress = [
    'pobockaFirmyUlice' => 'Houškova 35',
    'pobockaFirmyMesto' => '326 00 Plzeň 2',
    
]

?>

    <div id="chci-navazat-kontakt">
       <?php
       
        
        include ConfigurationCache::eventTemplates()['templates']."presenter-contacts.php";
        ?>
    </div>