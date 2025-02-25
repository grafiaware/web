<?php
use Site\ConfigurationCache;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;

$letakAttributesClass = ['class' => 'letak-v-igelitce'];

$posters = [];
$pathToFolder = ConfigurationCache::files()['@presenter']."/".$companyName."/poster/";
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
            'src' => ConfigurationCache::files()['@presenter']."/".$companyName."/poster/$poster.jpg",
            'alt' => "$poster",
        ],
        'downloadAttributes' => [
            'href' => ConfigurationCache::files()['@presenter']."/".$companyName."/poster/$poster.pdf",
            'download' => "$poster",
        ]
    ];
};

include ConfigurationCache::eventTemplates()['templates']."presenter-posters.php"; 

?>