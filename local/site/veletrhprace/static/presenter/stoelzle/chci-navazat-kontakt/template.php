<?php
use Site\ConfigurationCache;
use Events\Middleware\Events\ViewModel\EventViewModel;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;

$headline = 'Chci navázat kontakt';

$corporateContacts = [
    [
        'kontaktniOsoba' => 'Anita Glaserová',
        'funkce' => '',
        'telefon' => '606 030 600, čas 9-11h.',
        'email' => 'Anita.Glaserova@stoelzle.com',
    ],
    [
        'kontaktniOsoba' => 'Michaela Šebová',
        'funkce' => 'HR Manager',
        'telefon' => 'tel. 702 004 015, čas 14-16 h.',
        'email' => 'michaela.sebova@stoelzle.com',
    ],
];
$corporateAddress = [
    'pobockaFirmyUlice' => 'U Sklárny 300',
    'pobockaFirmyMesto' => '330 24 Heřmanova Huť',
]

?>

    <div id="chci-navazat-kontakt">
       <?php
       
        
        include ConfigurationCache::componentController()['templates']."paper/presenter-contacts.php";
        ?>
    </div>