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
    ['nazev' => 'Seřizovač ','kategorie' => [3],'mistoVykonu' => 'Dýšina ','vzdelani' =>3,'popisPozice' => 'Zajišťuje plynulý chod výrobních linek, nastavuje parametry,  provádí výměnu vstřikovacích forem. ','pozadujeme' => ['<p> • zájem o techniku, </p> <p> • chuť řešit problémy, </p> <p> • analytické uvažování, </p> <p> • zájem o znalost PLC (Simatic) výhodou, </p> <p> • chuť se neustále rozvíjet a posouvat, </p> <p> • vhodné i pro absolventy</p>'],'nabizime' => ['<p> • zaškolení zkušeným pracovníkem,  <p> • samostatnou a zodpovědnou pozici,  <p> • stravenky v hodnotě 120 Kč,  <p> • firemní bus z Plzně, <p> • dovolenou navíc,  <p> • měsíční bonusy,  <p> • zaměstnanecké výhody - až 9.000Kč/rok']],
    ['nazev' => 'Mechatronik ','kategorie' => [3],'mistoVykonu' => 'Dýšina ','vzdelani' =>3,'popisPozice' => 'diagnostikuje a provádí opravy na automatických linkách, provádí preventivní údržbu na zařzení, spolupracuje na zefektňování procesů díky automatizaci','pozadujeme' => ['<p> • SOU/SŠ elektro, </p> <p> • vyhláška 50/§6, </p> <p> • znalost PLC (Simatic), </p> <p> • základní znalost pneumatiky, </p> <p> • mechaniky, elektroniky, </p> <p> • zájem o kamarové systémy výhodou, </p> <p> • schopnost stanovit si priority  </p>'],'nabizime' => ['<p> • zaškolení zkušeným pracovníkem,  <p> • samostatnou a zodpovědnou pozici,  <p> • stravenky v hodnotě 120 Kč,  <p> • firemní bus z Plzně, <p> • dovolenou navíc,  <p> • měsíční bonusy,  <p> • zaměstnanecké výhody - až 9.000Kč/rok']],
    ['nazev' => 'Nástrojař ','kategorie' => [3],'mistoVykonu' => 'Dýšina ','vzdelani' =>3,'popisPozice' => 'provádí opravy vstřikovacích nástrojů (forem) ','pozadujeme' => ['<p> • technické vzdělání, </p> <p> • představivost, </p> <p> • čtení technických výkresů, </p> <p> • znalsot mikronavařování TIG, </p> <p> • základy broušení naplocho, </p> <p> • znalost obráběn na soustruhu a frézce</p>'],'nabizime' => ['<p> • zaškolení zkušeným pracovníkem,  <p> • samostatnou a zodpovědnou pozici,  <p> • stravenky v hodnotě 120 Kč,  <p> • firemní bus z Plzně, <p> • dovolenou navíc,  <p> • měsíční bonusy,  <p> • zaměstnanecké výhody - až 9.000Kč/rok']],
    ['nazev' => 'Operátor /-ka ','kategorie' => [1],'mistoVykonu' => 'Dýšina ','vzdelani' =>1,'popisPozice' => 'Obsluhuje výrobní zařízení, provádí vizuelní kontrolu hotového výrobku, v případě potřeby opracovává výrobek za pomoci nářadí do finální podoby ','pozadujeme' => ['<p> • dobrý zrak, </p> <p> • znalost českého jazyka, </p> <p> • pečlivost, </p> <p> • samostatnost, </p> <p> • ochotu pracovat ve 3 směnném provozu </p>'],'nabizime' => ['<p> • zaškolení zkušeným pracovníkem,  <p> • samostatnou a zodpovědnou pozici,  <p> • stravenky v hodnotě 120 Kč,  <p> • firemní bus z Plzně, <p> • dovolenou navíc,  <p> • měsíční bonusy,  <p> • zaměstnanecké výhody - až 9.000Kč/rok']],
    
    
]


?>

    <div id="pracovni-pozice">
       <?php
       
        
        include Configuration::componentControler()['templates']."presenter-job/template.php";
        ?>
    </div>