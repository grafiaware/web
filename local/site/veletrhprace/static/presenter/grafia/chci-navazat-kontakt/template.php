<?php
use Site\ConfigurationCache;
use Events\Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;

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
    'pobockaFirmyUlice' => 'Budilova 4',
    'pobockaFirmyMesto' => '301 00 Plzeň',
    
]

?>

    <div id="chci-navazat-kontakt">
       <?php
       
        
        include ConfigurationCache::componentController()['templates']."paper/presenter-contacts.php";
        ?>
    </div>