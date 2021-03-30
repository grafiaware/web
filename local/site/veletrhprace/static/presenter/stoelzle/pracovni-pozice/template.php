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
    ['nazev' => 'Třídička','kategorie' => [1],'mistoVykonu' => ' Heřmanova Huť ','vzdelani' =>2,'popisPozice' => 'Ručně třídit vyrobené zboží dle pokynů; Provádět dotřiďování výrobků na lince po vytřízení kontrolními automaty; Rovnat a stavět lahvičky, vyřazovat prasklé či deformované výrobky před vstupem do kontrolních automatů ;Balení výrobků dle balicího předpisu; Přetřizování výrobků zastaveného zboží; Číslování palet paletovým lístkem s údaji o době výroby','pozadujeme' => ['Ochota pracovat ve směnném provozu'],'nabizime' => ['<p> • Průměrná měsíční mzda je 25 700 Kč. </p> <p> • Práci v perspektivní a stabilní společnosti s dlouholetou tradicí; </p> <p> • dobré finanční ohodnocení; </p> <p> • 25 dní dovolené; </p> <p> • firemní benefity-13. plat, </p> <p> • náborový příspěvek 10 000 Kč, </p> <p> • výkonnostní odměny, </p> <p> • příplatek za dodržení pracovního fondu 4500 Kč,</p> <p> • stravenky 100 Kč, </p> <p> • příspěvek na dopravu, </p> <p> • roční bonus, </p> <p> • možnost ubytování, </p> <p> • přiveď kolegu a řekni si o odměnu 5000 Kč</p>']],
    ['nazev' => 'Strojník','kategorie' => [3],'mistoVykonu' => 'Heřmanova Huť','vzdelani' =>2,'popisPozice' => 'Obsluhovat stroj, provádět menší zásahy do chodu stroje, opravovat drobné závady; Ve spolupráci s vrchním strojníkem kontrolovat kvalitu výroby a odstraňovat vady výrobků; Plnit úkoly zadané nadřízeným zaměstnancem; Provádět záznamy o chodu stroje v průběhu směny a předávat pracoviště následující směně; Provádět úklid prostoru svého pracoviště','pozadujeme' => ['Ochota pracovat ve směnném provozu Zodpovědnost za uvedenou práci, za předávání informací následující směně'],'nabizime' => ['<p> • Průměrná měsíční mzda je 25 700 Kč. </p> <p> • Práci v perspektivní a stabilní společnosti s dlouholetou tradicí; </p> <p> • dobré finanční ohodnocení; </p> <p> • 25 dní dovolené; </p> <p> • firemní benefity-13. plat, </p> <p> • náborový příspěvek 10 000 Kč, </p> <p> • výkonnostní odměny, </p> <p> • příplatek za dodržení pracovního fondu 4500 Kč,</p> <p> • stravenky 100 Kč, </p> <p> • příspěvek na dopravu, </p> <p> • roční bonus, </p> <p> • možnost ubytování, </p> <p> • přiveď kolegu a řekni si o odměnu 5000 Kč</p>']],
    ['nazev' => 'Seřizovač kontrolních automatů','kategorie' => [3],'mistoVykonu' => 'Heřmanova Huť','vzdelani' =>3,'popisPozice' => 'Provádět kontrolu a seřízení kontrolních systémů dle kontrolního a měřícího plánu na směnách dohlížet na chod balících automatů a provádět drobné opravy nutné pro provoz těchto strojů při výměně na jiný druh sortimentu se podílí spolu s vrchním seřizovačem na přestavbě těchto strojů ','pozadujeme' => [''],'nabizime' => ['<p> • Průměrná měsíční mzda je 25 700 Kč. </p> <p> • Práci v perspektivní a stabilní společnosti s dlouholetou tradicí; </p> <p> • dobré finanční ohodnocení; </p> <p> • 25 dní dovolené; </p> <p> • firemní benefity-13. plat, </p> <p> • náborový příspěvek 10 000 Kč, </p> <p> • výkonnostní odměny, </p> <p> • příplatek za dodržení pracovního fondu 4500 Kč,</p> <p> • stravenky 100 Kč, </p> <p> • příspěvek na dopravu, </p> <p> • roční bonus, </p> <p> • možnost ubytování, </p> <p> • přiveď kolegu a řekni si o odměnu 5000 Kč</p>']],
    
]


?>

    <div id="pracovni-pozice">
       <?php
       
        
        include Configuration::componentControler()['templates']."presenter-job/template.php";
        ?>
    </div>