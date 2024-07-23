<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

/** @var PhpTemplateRendererInterface $this */

include 'data.php';

$katalogGenerator = new Katalog($container);

try {
    $katalog = $katalogGenerator->getKatalog();         
} catch (Exception $exc) {
    echo $exc->getMessage();
}

$volume  = array_column($katalog, 'firstLetter');
array_multisort($volume, SORT_ASC, $katalog);

//$katalogUid = $katalogGenerator->getLastKatalogUid();

$first='';
$chSet = [];
$chBlock = [];
$chBlocks = [];

foreach ($katalog as $client) {
    if (($client['uid'])  or ($client['nazev']) ) {           
            if (  $client['firstLetter'] != $first   )  {   //str_starts_with    
                //nove pismeno tj. zmena pismena
               if  (count($chBlock)) {
                   $chBlocks[] =  $chBlock ;
               }
               $chBlock = [];                
               $first = $client['firstLetter']; //strtoupper( substr($client['anchor'],0,1) );                
               $chSet[] =  [ 'chNazev' => $first , 'katalogUid' => $katalogUid  ]; 
            }
            $chBlock ['pismeno'] = $first; 
            $chBlock ['klienti'][] = $client;
    } //if
}
if  (count($chBlock)) {
    $chBlocks[] =  $chBlock;
}  
?>


<h2 style="text-align: center; margin-bottom: 30px;">*Umělci, kteří Vám představí svoji tvorbu.*</h2>


    <?=  "|" . $this->repeat(__DIR__.'/katalog-chset.php', $chSet) ?>


    <?=        $this->repeat(__DIR__.'/katalog-blockset.php', $chBlocks) ?>


