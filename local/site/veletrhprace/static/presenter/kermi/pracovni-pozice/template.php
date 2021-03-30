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
    ['nazev' => 'Strojírenský dělník - broušení elementů','kategorie' => [1],'mistoVykonu' => 'Stříbro','vzdelani' =>3,'popisPozice' => 'strojní broušení výrobků','pozadujeme' => ['<p>• praxe ve strojírenské výrobě</p>  <p>• ochota pracovat na směny</p>'],'nabizime' => ['<p>• Zázemí stabilní zahraniční společnosti</p>  <p>• Letní a vánoční prémie</p>  <p>• 5 týdnů dovolené</p>  <p>• Příspěvek na stravování ve výši 44,00 Kč</p>  <p>• Pravidelné roční navyšování mezd</p>  <p>• Příspěvek na dopravu až 5.400,00 Kč/měsíc</p>  <p>• Penzijní připojištění</p>  <p>• Příspěvek na sportovní a kulturní aktivity</p>  <p>• Výhodné tarify T-Mobile</p>  <p>• Odměny k životním i pracovním jubileím</p>  <p>• Očkování proti chřipce, vitamíny, ovoce</p>']],
    ['nazev' => 'Strojírenský dělník - navařování elementů','kategorie' => [1],'mistoVykonu' => 'Stříbro','vzdelani' =>3,'popisPozice' => 'strojní svařování výrobků odporovou metodou','pozadujeme' => ['<p>• praxe ve strojírenské výrobě</p>  <p>• ochota pracovat na směny</p>'],'nabizime' => ['<p>• Zázemí stabilní zahraniční společnosti</p>  <p>• Letní a vánoční prémie</p>  <p>• 5 týdnů dovolené</p>  <p>• Příspěvek na stravování ve výši 44,00 Kč</p>  <p>• Pravidelné roční navyšování mezd</p>  <p>• Příspěvek na dopravu až 5.400,00 Kč/měsíc</p>  <p>• Penzijní připojištění</p>  <p>• Příspěvek na sportovní a kulturní aktivity</p>  <p>• Výhodné tarify T-Mobile</p>  <p>• Odměny k životním i pracovním jubileím</p>  <p>• Očkování proti chřipce, vitamíny, ovoce</p>']],
    ['nazev' => 'Svářeč','kategorie' => [1],'mistoVykonu' => 'Stříbro','vzdelani' =>3,'popisPozice' => 'svařování radiátorů metodou CO2, TIG, WIG, autogen','pozadujeme' => ['<p>• praxe ve strojírenské výrobě</p>  <p>• ochota pracovat na směny</p>'],'nabizime' => ['<p>• Zázemí stabilní zahraniční společnosti</p>  <p>• Letní a vánoční prémie</p>  <p>• 5 týdnů dovolené</p>  <p>• Příspěvek na stravování ve výši 44,00 Kč</p>  <p>• Pravidelné roční navyšování mezd</p>  <p>• Příspěvek na dopravu až 5.400,00 Kč/měsíc</p>  <p>• Penzijní připojištění</p>  <p>• Příspěvek na sportovní a kulturní aktivity</p>  <p>• Výhodné tarify T-Mobile</p>  <p>• Odměny k životním i pracovním jubileím</p>  <p>• Očkování proti chřipce, vitamíny, ovoce</p>']],
    
]


?>

    <div id="pracovni-pozice">
       <?php
       
        
        include Configuration::componentControler()['templates']."presenter-job/template.php";
        ?>
    </div>