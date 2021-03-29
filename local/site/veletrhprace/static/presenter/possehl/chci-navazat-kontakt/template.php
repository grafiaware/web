<?php
use Site\Configuration;
use Model\Arraymodel\EventList;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

$headline = 'Chci navázat kontakt';

$corporateData = [
    [
        'kontaktniOsoba' => 'Petra Filipová',
        'funkce' => '',
        'telefon' => '+420 727 818 230',
        'email' => 'Petra.Filipova@possehlelectronics.com',
        'pobockaFirmyUlice' => 'Dýšina 297',
        'pobockaFirmyMesto' => '330 02 Dýšina',
    ]
];

?>

    <div id="chci-navazat-kontakt">
       <?php
       
        
        include Configuration::componentControler()['templates']."presenter-contacts/template.php";
        ?>
    </div>