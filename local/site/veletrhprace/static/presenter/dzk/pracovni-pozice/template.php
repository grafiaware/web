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
    
    ['nazev' => 'Expedient hotových výrobků','kategorie' => [2],'mistoVykonu' => 'Klatovy','vzdelani' =>2,'popisPozice' => 'obchodní oddělení','pozadujeme' => ['<p> • znalost práce na PC</p> <p> • dobrý zdravotní stav bez omezení (práce v chladu, noční práce)</p> <p> • samostatnost, zodpovědnost, flexibilita</p> <p> • třísměnný provoz</p>'],'nabizime' => ['<p> • MZDA 24 - 36.000 Kč, náborový příspěvek do výše 20 000 Kč</p> <p> • poukázky na zvýhodněný nákup našich výrobků</p> <p> • závodní stravování s výrazným příspěvkem zaměstnavatele</p> <p> • 5 týdnů dovolené</p> <p> • roční odměny</p> <p> • příspěvek na ubytování pro zaměstnance mimo region</p> <p> • příspěvek na penzijní připojištění</p> <p> • příspěvek na dopravu do zaměstnání a zpět</p> <p> • jednorázový bonus na dovolenou 3 000 Kč</p> <p> • věrnostní bonus za odpracované roky</p> <p> • příspěvky ze sociálního fondu na kulturní, sportovni</p>́ <p> • a vzdělávací akce ve výši 2 000 Kč a na dovolenou 2 000 Kč</p>']],
    ['nazev' => 'Pracovník dispečinku','kategorie' => [2],'mistoVykonu' => 'Klatovy','vzdelani' =>4,'popisPozice' => 'obchodní oddělení','pozadujeme' => ['<p> • znalost práce na PC </p> <p> • komunikativnost, zodpovědnost, pečlivost, schopnost rychlého rozhodování</p> <p> • třísměnný provoz, úvazek 37,5 h/týdně</p> <p> • vhodné pro absolventy</p>'],'nabizime' => ['<p> • MZDA 22 - 31.000 Kč, náborový příspěvek do výše 20 000 Kč</p> <p> • poukázky na zvýhodněný nákup našich výrobků</p> <p> • závodní stravování s výrazným příspěvkem zaměstnavatele</p> <p> • 5 týdnů dovolené</p> <p> • roční odměny</p> <p> • příspěvek na ubytování pro zaměstnance mimo region</p> <p> • příspěvek na penzijní připojištění</p> <p> • příspěvek na dopravu do zaměstnání a zpět</p> <p> • jednorázový bonus na dovolenou 3 000 Kč</p> <p> • věrnostní bonus za odpracované roky</p> <p> • příspěvky ze sociálního fondu na kulturní, sportovní</p> <p> • a vzdělávací akce ve výši 2 000 Kč a na dovolenou 2 000 Kč</p>']],
    ['nazev' => 'Investiční technik','kategorie' => [3],'mistoVykonu' => 'Klatovy','vzdelani' =>7,'popisPozice' => 'technické oddělení','pozadujeme' => ['<p> • vzdělání VŠ technické (možnost i pro absolventy), případně SŠ technické s praxí AutoCAD LT (zpracování výkresové dokumentace 2D)</p> <p> • Zkušenosti s vedením týmu pracovníků výhodou</p> <p> • AJ popř. NJ výhodou</p> <p> • Řidičské oprávnění sk B</p> <p> • Dobrý zdravotní stav</p> <p> • Komunikativní, zodpovědný, cílevědomý</p> <p> • Časová flexibilita</p> <p> • Analytické uvažování</p> '],'nabizime' => ['<p> • MZDA 39 - 52.000 Kč, </p> <p> • náborový příspěvek do výše 20 000 Kč </p> <p> • poukázky na zvýhodněný nákup našich výrobků závodní stravování s výrazným příspěvkem zaměstnavatele </p> <p> • 5 týdnů dovolené roční odměny příspěvek na ubytování pro zaměstnance mimo region </p> <p> • příspěvek na penzijní připojištění příspěvek na dopravu do zaměstnání a zpět </p> <p> • jednorázový bonus na dovolenou 3 000 Kč věrnostní bonus za odpracované roky</p> <p> •  příspěvky ze sociálního fondu na kulturní, sportovní a vzdělávací akce ve výši 2 000 Kč a na dovolenou 2 000 Kč</p>']],
    ['nazev' => 'Mistr výrobního provozu','kategorie' => [1],'mistoVykonu' => 'Klatovy','vzdelani' =>4,'popisPozice' => 'výrobní oddělení','pozadujeme' => ['<p> • vzdělání ÚSO popř. VŠ technologie zpracování potravin (masa)</p> <p> • zkušenosti s vedením pracovního kolektivu, koordinace výroby</p> <p> • flexibilita, komunikativnost, zodpovědnost</p> <p> • znalost práce s PC</p> <p> • zdravotní stav bez omezení</p> <p> • třísměnný provoz </p>'],'nabizime' => ['<p> • MZDA 30-45.000 Kč, </p> <p> • náborový příspěvek do výše 20 000 Kč</p> <p> • poukázky na zvýhodněný nákup našich výrobků </p> <p> • závodní stravování s výrazným příspěvkem zaměstnavatele </p> <p> • 5 týdnů dovolené roční odměny příspěvek na ubytování pro zaměstnance mimo region </p> <p> • příspěvek na penzijní připojištění příspěvek na dopravu do zaměstnání a zpět </p> <p> • jednorázový bonus na dovolenou 3 000 Kč věrnostní bonus za odpracované roky příspěvky ze sociálního fondu na kulturní, sportovní a vzdělávací akce ve výši 2 000 Kč a na dovolenou 2 000 Kč</p>']],
    ['nazev' => 'Předák výrobního provozu','kategorie' => [1],'mistoVykonu' => 'Klatovy','vzdelani' =>4,'popisPozice' => 'výrobní oddělení','pozadujeme' => ['<p> • SO popř. ÚSO potravinářské obory výhodou</p> <p> • zkušenosti s vedením pracovního kolektivu, koordinace výroby</p> <p> • flexibilita, komunikativnost, zodpovědnost</p> <p> • znalost práce s PC</p> <p> • zdravotní stav bez omezení</p> <p> • třísměnný provoz</p>'],'nabizime' => ['<p> • MZDA 26 - 35.000 Kč</p> <p> • náborový příspěvek do výše 20 000 Kč</p> <p> • poukázky na zvýhodněný nákup našich výrobků </p> <p> • závodní stravování s výrazným příspěvkem zaměstnavatele </p> <p> • 5 týdnů dovolené roční odměny příspěvek na ubytování pro zaměstnance mimo region </p> <p> • příspěvek na penzijní připojištění příspěvek na dopravu do zaměstnání a zpět </p> <p> • jednorázový bonus na dovolenou 3 000 Kč věrnostní bonus za odpracované roky příspěvky ze sociálního fondu na kulturní, sportovní a vzdělávací akce ve výši 2 000 Kč a na dovolenou 2 000 Kč</p>']],
    ['nazev' => 'Míchač','kategorie' => [1],'mistoVykonu' => 'Klatovy','vzdelani' =>2,'popisPozice' => 'výrobní oddělení','pozadujeme' => ['<p> • Základní vzdělání popř SO a další</p> <p> • Zodpovědnost, chuť se učit novým věcem</p> <p> • Zdravotní stav bez omezení</p> <p> • Třísměnný provoz</p>'],'nabizime' => ['<p> • MZDA 27 - 37.000 Kč  </p> <p> • náborový příspěvek do výše 20 000 Kč</p> <p> • poukázky na zvýhodněný nákup našich výrobků </p> <p> • závodní stravování s výrazným příspěvkem zaměstnavatele </p> <p> • 5 týdnů dovolené roční odměny příspěvek na ubytování pro zaměstnance mimo region </p> <p> • příspěvek na penzijní připojištění příspěvek na dopravu do zaměstnání a zpět </p> <p> • jednorázový bonus na dovolenou 3 000 Kč věrnostní bonus za odpracované roky příspěvky ze sociálního fondu na kulturní, sportovní a vzdělávací akce ve výši 2 000 Kč a na dovolenou 2 000 Kč</p>']],
    ['nazev' => 'Obsluha udíren','kategorie' => [1],'mistoVykonu' => 'Klatovy','vzdelani' =>2,'popisPozice' => 'výrobní oddělení','pozadujeme' => ['<p> • Základní vzdělání popř SO a další</p> <p> • Zodpovědnost, chuť se učit novým věcem</p> <p> • Zdravotní stav bez omezení</p> <p> • Třísměnný provoz</p>'],'nabizime' => ['MZDA | 25 - 36.000 Kčy náborový příspěvek do výše 20 000 Kč poukázky na zvýhodněný nákup našich výrobků závodní stravování s výrazným příspěvkem zaměstnavatele 5 týdnů dovolené roční odměny příspěvek na ubytování pro zaměstnance mimo region příspěvek na penzijní připojištění příspěvek na dopravu do zaměstnání a zpět jednorázový bonus na dovolenou 3 000 Kč věrnostní bonus za odpracované roky příspěvky ze sociálního fondu na kulturní, sportovní a vzdělávací akce ve výši 2 000 Kč a na dovolenou 2 000 Kč']],
    ['nazev' => 'Obsluha balících a etiketovacích strojů','kategorie' => [1],'mistoVykonu' => 'Klatovy','vzdelani' =>2,'popisPozice' => 'výrobní oddělení','pozadujeme' => ['<p> • Vzdělání od základního výše, možnost zaškolení</p> <p> • znalost práce na PC</p> <p> • dobrý zdravotní stav bez omezení (práce v chladu, noční práce)</p> <p> • samostatnost, zodpovědnost, flexibilita</p> <p> • třísměnný provoz</p>'],'nabizime' => ['<p> • MZDA 15 - 25.000 Kč </p> <p> • náborový příspěvek do výše 20 000 Kč</p> <p> • poukázky na zvýhodněný nákup našich výrobků </p> <p> • závodní stravování s výrazným příspěvkem zaměstnavatele </p> <p> • 5 týdnů dovolené roční odměny příspěvek na ubytování pro zaměstnance mimo region </p> <p> • příspěvek na penzijní připojištění příspěvek na dopravu do zaměstnání a zpět </p> <p> • jednorázový bonus na dovolenou 3 000 Kč věrnostní bonus za odpracované roky příspěvky ze sociálního fondu na kulturní, sportovní a vzdělávací akce ve výši 2 000 Kč a na dovolenou 2 000 Kč</p>']],
    ['nazev' => 'Obsluha narážek','kategorie' => [1],'mistoVykonu' => 'Klatovy','vzdelani' =>2,'popisPozice' => 'výrobní oddělení','pozadujeme' => ['<p> • Vzdělání od základního výše, možnost zaškolení</p> <p> • znalost práce na PC</p> <p> • dobrý zdravotní stav bez omezení (práce v chladu, noční práce)</p> <p> • samostatnost, zodpovědnost, flexibilita</p> <p> • třísměnný provoz</p>'],'nabizime' => ['<p> • MZDA 15 - 25.000 Kč </p> <p> • náborový příspěvek do výše 20 000 Kč</p> <p> • poukázky na zvýhodněný nákup našich výrobků </p> <p> • závodní stravování s výrazným příspěvkem zaměstnavatele </p> <p> • 5 týdnů dovolené roční odměny příspěvek na ubytování pro zaměstnance mimo region </p> <p> • příspěvek na penzijní připojištění příspěvek na dopravu do zaměstnání a zpět </p> <p> • jednorázový bonus na dovolenou 3 000 Kč věrnostní bonus za odpracované roky příspěvky ze sociálního fondu na kulturní, sportovní a vzdělávací akce ve výši 2 000 Kč a na dovolenou 2 000 Kč</p>']],
    ['nazev' => 'Pracovník k provozní lince výroby','kategorie' => [1],'mistoVykonu' => 'Klatovy','vzdelani' =>2,'popisPozice' => 'výrobní oddělení','pozadujeme' => ['<p> • Základní vzdělání popř SO a další</p> <p> • Zodpovědnost, chuť se učit novým věcem</p> <p> • Zdravotní stav bez omezení</p> <p> • Třísměnný provoz</p>'],'nabizime' => ['<p> • MZDA 15 - 25.000 Kč </p> <p> • náborový příspěvek do výše 20 000 Kč</p> <p> • poukázky na zvýhodněný nákup našich výrobků </p> <p> • závodní stravování s výrazným příspěvkem zaměstnavatele </p> <p> • 5 týdnů dovolené roční odměny příspěvek na ubytování pro zaměstnance mimo region </p> <p> • příspěvek na penzijní připojištění příspěvek na dopravu do zaměstnání a zpět </p> <p> • jednorázový bonus na dovolenou 3 000 Kč věrnostní bonus za odpracované roky příspěvky ze sociálního fondu na kulturní, sportovní a vzdělávací akce ve výši 2 000 Kč a na dovolenou 2 000 Kč</p>']],
    
]


?>

    <div id="pracovni-pozice">
       <?php
       
        
        include Configuration::componentControler()['templates']."presenter-job/template.php";
        ?>
    </div>