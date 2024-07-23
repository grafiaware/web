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

$first='';
$chSet = [];
$chBlock = [];
$chBlocks = [];

foreach ($katalog as $client) {
    if (($client['nazev-instituce'])  or ($client['prijmeni']) ) {           
            if ( (substr($client['prijmeni'],0,1) != $first)   )  {   //str_starts_with    
                //nove pismeno tj. zmena pismena
               if  (count($chBlock)) {
                   $chBlocks[] =  $chBlock ;
               }
               $chBlock = [];                
               $first = substr($client['prijmeni'],0,1);                
               $chSet[] =  [ 'chNazev' => substr($client['prijmeni'],0,1), 'thisSite' => $thisSite  ]; 
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


