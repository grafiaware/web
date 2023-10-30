<?php
use Site\ConfigurationCache;
use Events\Middleware\Events\ViewModel\EventViewModel;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;

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
       
        include ConfigurationCache::eventTemplates()['templates']."paper/presenter-contacts.php";
        ?>
    </div>