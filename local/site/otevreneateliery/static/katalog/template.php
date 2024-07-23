<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

/** @var PhpTemplateRendererInterface $this */

$institutionsSite = '6671583cb7df7';
$personsSite = '667153b3adac6';
$thisSite = '6697cf115d654';


$katalog=[]; // podle nazev-instituce rozpoznavat instituce/osoba
$katalog[]= array ( 'prijmeni' => 'Ateliér s duší', 'jmeno' => '', 
                    'nazevInstituce' => 'Ateli&eacute;r s du&scaron;&iacute; ',  
                    'webAnchor' => 'atelier-s-dusi',
                    'site'  =>  $institutionsSite );
$katalog[]= array ( 'prijmeni' => 'Arteterapie Plzeň', 'jmeno' => '', 
                    'nazevInstituce' => 'Arteterapie Plzeň',
                    'webAnchor' => 'arteterapie-plzen',
                    'site'  =>  $institutionsSite );
$katalog[]= array ( 'prijmeni' => 'Ateliér Dorido - Petra Fenclová', 'jmeno' => 'Ateli&eacute;r Dorido - Petra Fenclov&aacute;', 
                    'nazevInstituce' => '',
                    'webAnchor' => 'atelier-dorido-petra-fenclova',
                    'site'  =>  $institutionsSite );

$katalog[]= array ( 'prijmeni' => 'Pankrác', 'jmeno' => 'Marketa  ', 'nazevInstituce' => '',
                    'webAnchor' => '',
                    'site'  =>  $personsSite );
$katalog[]= array ( 'prijmeni' => 'Lišková Mašková', 'jmeno' => 'Jaroslava', 'nazevInstituce' => '',
                    'webAnchor' => 'Li&scaron;kov&aacute; Ma&scaron;kov&aacute; Jaroslava',
                    'site'  =>  $personsSite );
$katalog[]= array ( 'prijmeni' => 'Liska Nordlinder', 'jmeno' => 'Magdalena', 'nazevInstituce' => '',
                    'webAnchor' => 'Liska Nordlinder Magdalena',
                    'site'  =>  $personsSite );

$katalog[]= array ( 'prijmeni' => '', 'jmeno' => '', 'nazevInstituce' => '', 'webAnchor' => '' ,  'site'  => ''  );


$volume  = array_column($katalog, 'prijmeni');
array_multisort($volume, SORT_ASC, $katalog);

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


