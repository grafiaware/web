<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

$headline = 'Chci nazávat kontakt';

$corporateData = [
    [
        'kontaktniOsoba' => 'Radka Novotná',
        'funkce' => 'asistentka',
        'telefon' => '+420 758 659 855',
        'email' => 'firma@firmovata.cz',
        'pobockaFirmyUlice' => 'U velkého poníka 417',
        'pobockaFirmyMesto' => '800 45 Poníkov',
    ]
];
        
$personalData = [
    [
        'fotografie' => [
            'src' => 'images/moje-krasna-fotka.jpg',
            'alt' => 'Profilový obrázek',
            'width' => '',
            'height' => '',
        ],
        'jmeno' => 'Lukáš Novák',
        'email' => 'novak@nereknu.cz',
        'telefon' => '+420 123 456 789',
        'pracPopis' => 'Momentálně bez práce',
        'nahraneSoubory' => [
            'zivotopis' => 'cesta k souboru',
        ],
    ],
    'button' => 'index.php'
];
        
?>