<?php
use Site\Configuration;
use Model\Arraymodel\EventList;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

$headline = 'Chci navázat kontakt';

$corporateData = [
    [
        'kontaktniOsoba' => 'Grafia',
        'funkce' => 'sekretariát',
        'telefon' => '+420 377 227 701',
        'email' => 'sekretariat@grafia.cz',
        'pobockaFirmyUlice' => 'Budilova 4',
        'pobockaFirmyMesto' => '301 00 Plzeň',
    ]
];

?>

    <div id="chci-navazat-kontakt">
       <?php
       
        
        include Configuration::componentControler()['templates']."presenter-contacts/template.php";
        ?>
    </div>