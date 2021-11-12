<?php
use Site\Configuration;
use Events\Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;

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
       
        include Configuration::componentController()['templates']."paper/presenter-contacts.php";
        ?>
    </div>