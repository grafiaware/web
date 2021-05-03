<?php
use Site\Configuration;
use Model\Arraymodel\Event;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

$headline = 'Chci navázat kontakt';

$corporateContacts = [
    [
        'kontaktniOsoba' => 'Lucie Černá',
        'funkce' => 'dělnické pozice',
        'telefon' => '377 198 857',
        'email' => 'lucie.cerna@md-elektronik.cz',
    ],
    [
        'kontaktniOsoba' => 'Kristýna Křížová',
        'funkce' => 'odborné & administrativní pozice',
        'telefon' => '377 198 435',
        'email' => 'kristyna.krizova@md-elektronik.cz',
    ]
];
$corporateAddress = [
    'pobockaFirmyUlice' => 'Dobřanská 629',
    'pobockaFirmyMesto' => '332 14 Chotěšov',
    
]

?>

    <div id="chci-navazat-kontakt">
       <?php
       
        
        include Configuration::componentController()['templates']."presenter-contacts/template.php";
        ?>
    </div>