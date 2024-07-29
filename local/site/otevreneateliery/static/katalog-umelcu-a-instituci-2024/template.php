<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

/** @var PhpTemplateRendererInterface $this */

include 'data.php';

$katalogGenerator = new Katalog($container);

try {
    $katalog = $katalogGenerator->getKatalog();         
} catch (Exception $exc) {
    echo "<p style=\"background-color:yellow;\">{$exc->getMessage()}</p>";
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


<p class="nadpis nastred">Umělci, kteří Vám představí svoji tvorbu</p >

<p>Mapa Plzně a okolí se zaplní zastávkami, kde to 28. - 29. září 2024 ožije uměním. Pojďme se podívat, kdo otevře své ateliéry! Přinášíme Vám malou ochutnávku jejich děl. Pokud zde nenajdete profily všech umělců, je to proto, že některým jejich bohémská duše dosud nedopřála čas k odeslání podkladů pro tento web. ;-) </p>

<p class="nastred">
    <?=  "|" . $this->repeat(__DIR__.'/katalog-chset.php', $chSet) ?>
</p>

<div class="ui stackable grid">
    <div class="six wide column">
        <?=        $this->repeat(__DIR__.'/katalog-blockset.php', $chBlocks) ?>
    </div>
    <div class="ten wide column">
        <img class="" src="@siteimages/kolaz.jpg" alt="Koláž obrazů a fotografií z akce Víkend otevřených atelierů Plzeň tvořivá" />
    </div>
</div>



