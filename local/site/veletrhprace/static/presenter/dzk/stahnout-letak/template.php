<?php
use Site\Configuration;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;

$letakAttributesClass = ['class' => 'letak-v-igelitce'];

$posters = [];
$pathToFolder = Configuration::files()['presenter'].$shortName."/poster/";
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
            'src' => Configuration::files()['presenter'].$shortName."/poster/$poster.jpg",
            'alt' => "$poster",
        ],
        'downloadAttributes' => [
            'href' => Configuration::files()['presenter'].$shortName."/poster/$poster.pdf",
            'download' => "$poster",
        ]
    ];
};

include Configuration::componentController()['templates']."paper/presenter-posters.php"; 

?>