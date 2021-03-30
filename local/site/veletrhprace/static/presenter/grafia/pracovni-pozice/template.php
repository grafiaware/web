<?php
use Site\Configuration;
use Model\Arraymodel\EventList;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;

$headline = 'Pracovní pozice';
$perex = '';

//template:
//    repeat('vnitřek', $kategorie]
//$tag = [
//    1 => 'výroba/dělnická',
//    2 => 'administrativa/THP',
//    3 => 'technická',
//    4 => 'manažerská/vedoucí'
//];
//$kvalifikace = [
//    1 => 'Bez omezení',
//    2 => 'ZŠ',
//    3 => 'SOU bez maturity',
//    4 => 'SOU s maturitou',
//    5 => 'SŠ',
//    5 => 'VOŠ / Bc.',
//    5 => 'VŠ',
//];

$pracovniPozice = [
    ['nazev' => 'Operátor call centra ','kategorie' => [2],'mistoVykonu' => 'Plzeň','vzdelani' =>5,'popisPozice' => '<p>Jste výřeční a u telefonu jste jako ryba ve vodě? Přidejte se do našeho týmu - nudit se s námi určitě nebudete! :-)</p>  <p>Možnost zkráceného úvazku</p>  <p>Nástup IHNED</p>  <p> </p>  <p>Co Vás čeká:</p>  <p>Aktivní obvolávání již kontaktovaných firem – zprostředkování prezentace zaměstnavatelů v kariérní publikaci</p>','pozadujeme' => ['<p>• min. SŠ vzdělání</p>  <p>• komunikační schopnosti</p>  <p>• znalost MS Office</p>  <p>• pečlivost, důslednost, vytrvalost</p>'],'nabizime' => ['<p>• zázemí stabilní společnosti s dlouholetou tradicí ve vzdělávání dospělých</p>  <p>• zajímavou a různorodou práci</p>  <p>• přátelský kolektiv</p>  <p>• flexibilní pracovní dobu</p>  <p>• plný nebo zkrácený úvazek</p>  <p>• stravenky</p>']],
]


?>

    <div id="pracovni-pozice">
       <?php
       
        
        include Configuration::componentControler()['templates']."presenter-job/template.php";
        ?>
    </div>