<?php
use Site\Configuration;
use Model\Arraymodel\EventList;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

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
       
        
        include Configuration::componentControler()['templates']."presenter-contacts/template.php";
        ?>
    </div>