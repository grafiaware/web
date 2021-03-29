<?php
use Site\Configuration;
use Model\Arraymodel\EventList;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

$headline = 'Chci navázat kontakt';

$corporateData = [
    [
        'kontaktniOsoba' => 'Anita Glaserová',
        'funkce' => '',
        'telefon' => '606 030 600, čas 9-11h.',
        'email' => 'Anita.Glaserova@stoelzle.com',
        'pobockaFirmyUlice' => 'U Sklárny 300',
        'pobockaFirmyMesto' => '330 24 Heřmanova Huť',
    ],
    [
        'kontaktniOsoba' => 'Michaela Šebová',
        'funkce' => 'HR Manager',
        'telefon' => 'tel. 702 004 015, čas 14-16 h.',
        'email' => 'michaela.sebova@stoelzle.com',
        'pobockaFirmyUlice' => 'U Sklárny 300',
        'pobockaFirmyMesto' => '330 24 Heřmanova Huť',
    ],
];

?>

    <div id="chci-navazat-kontakt">
       <?php
       
        
        include Configuration::componentControler()['templates']."presenter-contacts/template.php";
        ?>
    </div>