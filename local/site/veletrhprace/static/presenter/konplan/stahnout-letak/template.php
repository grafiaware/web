<?php
use Site\Configuration;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

$letakAttributesClass = ['class' => 'letak-v-igelitce'];

$posters = [];
$pathToFolder = Configuration::componentControler()['presenterFiles'].$shortName."/poster/";
$globFolder = $pathToFolder."*.pdf";
$glob = glob($globFolder);
foreach($glob as $file) {
    $posters[] = explode(".", str_replace($pathToFolder, "", $file))[0];
}

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