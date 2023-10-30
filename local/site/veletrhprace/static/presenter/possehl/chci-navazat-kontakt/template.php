<?php
use Site\ConfigurationCache;
use Events\Middleware\Events\ViewModel\EventViewModel;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;

$headline = 'Chci navázat kontakt';

$corporateContacts = [
    [
        'kontaktniOsoba' => 'Petra Filipová',
        'funkce' => '',
        'telefon' => '373 731 162,  373 731 124',
        'email' => 'Petra.Filipova@possehlelectronics.com',
    ]
];
$corporateAddress = [
    'pobockaFirmyUlice' => 'Dýšina 297',
    'pobockaFirmyMesto' => '330 02 Dýšina',
    
]

?>

    <div id="chci-navazat-kontakt">
       <?php
       
        
        include ConfigurationCache::eventTemplates()['templates']."paper/presenter-contacts.php";
        ?>
    </div>