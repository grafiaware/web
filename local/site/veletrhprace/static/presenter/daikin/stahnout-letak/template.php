<?php
use Site\Configuration;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

$posters = [
        'Operátor výroby',
        'Trainee program',
        'Výrobní inženýr - Robotické aplikace, strojové vidění',
        'EN_Product Development Engineer Profile',
        
    ];
$letak = [];
foreach ($posters as $poster) {
    $letak[] = [
        'letakAttributes' => $letakAttributesClass +
        [
            'src' => Configuration::componentControler()['presenterFiles'].$shortName."/poster/$poster.jpg",
            'alt' => "$poster",
        ],
        'downloadAttributes' => [
            'href' => Configuration::componentControler()['presenterFiles'].$shortName."/poster/$poster.pdf",
            'download' => "$poster",
        ]
    ];
};

include Configuration::componentControler()['templates']."presenter-posters/template.php"; 

?>