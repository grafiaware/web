<?php
use Site\ConfigurationCache;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;

$letakAttributesClass = ['class' => 'letak-v-igelitce'];

$posters = [];
$pathToFolder = ConfigurationCache::files()['presenter'].$shortName."/poster/";
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
            'src' => ConfigurationCache::files()['presenter'].$shortName."/poster/$poster.jpg",
            'alt' => "$poster",
        ],
        'downloadAttributes' => [
            'href' => ConfigurationCache::files()['presenter'].$shortName."/poster/$poster.pdf",
            'download' => "$poster",
        ]
    ];
};

include ConfigurationCache::componentController()['templates']."paper/presenter-posters.php"; 

?>