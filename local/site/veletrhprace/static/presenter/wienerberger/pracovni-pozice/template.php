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
    ['nazev' => 'Dělník keramické výroby','kategorie' => [1],'mistoVykonu' => 'Stod','vzdelani' =>3,'popisPozice' => 'Náplní práce nového kolegy bude obsluha poloautomatických strojů na výrobu střešní krytiny, příprava sádrových forem, příp. nakládání pecních vozů, třídění pálených tašek či obsluha balicí linky.','pozadujeme' => ['<p>• zkušenosti s prací ve výrobě nebo s ovládáním strojů</p>  <p>• ochota pracovat v nepřetržitém provozu</p>  <p>• samostatnost a spolehlivost</p>  <p>• průkaz na VZV výhodou</p>  <p>• vazačské zkoušky výhodou,</p>  <p>Pozn.: Z důvodu občasného zvedání těžších břemen není pozice vhodná pro ženy.</p>'],'nabizime' => ['<p>• nástupní mzdu 25000 – 30000 Kč/měsíc (brutto)</p>  <p>• náborový příspěvek 30000 Kč</p>  <p>• práci v nepřetržitém provozu na 12-hodinové směny v režimu: 3 dny ranní (6:15 - 18:00) - 3 dny volno - 3 dny noční (18:15 - 6:00) - 3 dny volno</p>  <p>• zázemí stabilní společnosti</p>  <p>• zaškolení a možnost profesního růstu</p>  <p>• benefity společnosti – 5 týdnů dovolené, 13. mzdu, příspěvky na penzijní připojištění 1500 Kč/měs., zaměstnanecké slevy na produkty, příspěvky na stravování, příjemné pracovní prostředí, nápoje na pracovišti, firemní akce, právní poradenství, aj.</p>']],
    ['nazev' => 'Obsluha lisu','kategorie' => [1],'mistoVykonu' => 'Stod','vzdelani' =>3,'popisPozice' => 'Náplní práce nového kolegy bude obsluha poloautomatických strojů na výrobu střešní krytiny, především pak obsluha lisu.','pozadujeme' => ['<p>• vyučení v technickém oboru - ideálně jako automechanik nebo seřizovač</p>  <p>• zkušenosti s výrobou a ovládáním strojů</p>  <p>• ochota pracovat v nepřetržitém provozu</p>  <p>• samostatnost a spolehlivost</p>  <p>• zkušenost s obsluhováním několika zařízení najednou výhodou</p>  <p>• průkaz na VZV výhodou</p>  <p>• vazačské zkoušky výhodou</p>'],'nabizime' => ['<p>• nástupní mzdu 27000 – 35000 Kč/měsíc (brutto)</p>  <p>• náborový příspěvek 30000 Kč</p>  <p>• práci v nepřetržitém provozu na 12-hodinové směny v režimu: 3 dny ranní (6:15 - 18:00) - 3 dny volno - 3 dny noční (18:15 - 6:00) - 3 dny volno</p>  <p>• zázemí stabilní společnosti</p>  <p>• zaškolení a možnost profesního růstu</p>  <p>• benefity společnosti – 5 týdnů dovolené, 13. mzdu, příspěvky na penzijní připojištění 1500 Kč/měs., zaměstnanecké slevy na produkty, příspěvky na stravování, příjemné pracovní prostředí, nápoje na pracovišti, firemní akce, právní poradenství, aj.</p>']],
    ['nazev' => 'Provozní elektrikář','kategorie' => [3,1],'mistoVykonu' => 'Stod','vzdelani' =>3,'popisPozice' => 'Náplní práce nového kolegy bude preventivní údržba elektrických zařízení, opravy elektrických částí strojů, analýza problémů a jejich odstranění v rámci preventivní údržby. Úzká spolupráce se strojním zámečníkem. Příležitostná výpomoc ve výrobě.','pozadujeme' => ['<p>• SOU v oboru elektro</p>  <p>• vyhlášku 50 §6</p>  <p>• praxe v oboru vítána</p>  <p>• flexibilitu, spolehlivost a pečlivost</p>  <p>• ochota příležitostně zaskočit v případě potřeby za kolegu ve výrobě</p>'],'nabizime' => ['<p>• nástupní mzdu až 40000 Kč (dle praxe a zkušeností)</p>  <p>• náborový příspěvek 30000 Kč</p>  <p>• práce na dvě směny v režimu: 3 dny ranní - 3 dny volno - 3 dny noční</p>  <p>• rozmanitou práci v tradičním cihlářském průmyslu</p>  <p>• zázemí stabilní společnosti</p>  <p>• zaškolení a možnost profesního růstu</p>  <p>• benefity společnosti – 5 týdnů dovolené, 13. mzdu, příspěvky na penzijní připojištění 1000 Kč/měs., zaměstnanecké slevy na produkty, příspěvky na stravování, příjemné pracovní prostředí, nápoje na pracovišti, firemní akce, právní poradenství, aj.</p>']],
    ['nazev' => 'Řidič VZV','kategorie' => [1],'mistoVykonu' => 'Stod','vzdelani' =>3,'popisPozice' => 'Náplní práce nového kolegy bude obsluha vysokozdvižného vozíku, nakládka zboží pro zákazníka (expedice) nebo nakládka zboží ve výrobě a převoz na sklad.','pozadujeme' => ['<p>• praxe s obsluhou VZV</p>  <p>• samostatnost a spolehlivost</p>  <p>• platný průkaz na VZV výhodou</p>  <p>• vazačské zkoušky výhodou</p>'],'nabizime' => ['<p>• nástupní mzdu 25000 - 32000 Kč (brutto)</p>  <p>• náborový příspěvek 30000 Kč</p>  <p>• práci v nepřetržitém provozu na 12-hodinové směny v režimu: 3 dny ranní (6:15 - 18:00) - 3 dny volno - 3 dny noční (18:15 - 6:00) - 3 dny volno</p>  <p>• zázemí stabilní společnosti</p>  <p>• zaškolení a možnost profesního růstu</p>  <p>• benefity společnosti – 5 týdnů dovolené, 13. mzdu, příspěvky na penzijní připojištění 1500 Kč/měs., zaměstnanecké slevy na produkty, příspěvky na stravování, příjemné pracovní prostředí, nápoje na pracovišti, firemní akce, právní poradenství, aj.</p>']],
    ['nazev' => 'Řidič kolového nakladače','kategorie' => [1],'mistoVykonu' => 'Stod','vzdelani' =>3,'popisPozice' => 'Náplní práce nového kolegy bude obsluha kolového nakladače a výrobní linky, vč. běžné údržby.','pozadujeme' => ['<p>• ŘP sk. C</p>  <p>• samostatnost a spolehlivost</p>  <p>• praxe v obsluze stavebních strojů výhodou</p>'],'nabizime' => ['<p>• nástupní mzdu až 30000 Kč (brutto)</p>  <p>• náborový příspěvek 30000 Kč</p>  <p>• práci od pondělí do pátku na dvě 8hodinové směny: ranní 6:00 - 14:15,</p>  <p>• odpolední 13:15 - 21:30</p>  <p>• zázemí stabilní společnosti</p>  <p>• zaškolení a možnost profesního růstu</p>  <p>• benefity společnosti – 5 týdnů dovolené, 13. mzdu, příspěvky na penzijní připojištění 1500 Kč/měs., zaměstnanecké slevy na produkty, příspěvky na stravování, příjemné pracovní prostředí, nápoje na pracovišti, firemní akce, právní poradenství, aj.</p>']],
]


?>

    <div id="pracovni-pozice">
       <?php
       
        
        include Configuration::componentControler()['templates']."presenter-job/template.php";
        ?>
    </div>