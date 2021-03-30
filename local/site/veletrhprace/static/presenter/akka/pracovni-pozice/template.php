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
//    6 => 'VOŠ / Bc.',
//    7 => 'VŠ',
//];

$pracovniPozice = [
   
    ['nazev' => 'Zkušební technik pro lesní a zahradní techniku (M/Ž)','kategorie' => [3],'mistoVykonu' => 'Plzeň','vzdelani' =>4,'popisPozice' => '<p>• příprava dílů a celých strojů na mechanické testy </p>  <p>• obsluha testovacích zařízení </p>  <p>• mechanické zkoušky dílů a celků, popř. elektro testování vyhodnocení a zápis výsledků z testů rozborky/sborky celých zařízení a jejich uzlů </p>  <p>• práce s databázovými systémy a správa dat v nich obsažených </p>  <p>• práce a měření s laboratorním vybavením </p>  <p>• tvorba prezentací, školících a instruktážních materiálů </p>  <p>• komunikace se zákazníky a týmem za účelem odsouhlasení si obsahových stránek projektu a prezentace výsledků projektu </p>  <p>• služební cesty do zahraničí dle potřeby</p>','pozadujeme' => ['<p>• vzdělání či praxe v technickém oboru </p>  <p>• německý jazyk alespoň na pasivní úrovni, veškerá dokumentace je v němčině </p>  <p>• vyhláška 50 </p>  <p>• Výhodou zkušenosti z obalsti servisu, montáže, testování komunikativní znalost němčiny</p>'],'nabizime' => ['<p>• pružnou pracovní dobu </p>  <p>• 5 týdnů dovolené a vybírání náhradního volna </p>  <p>• osobnostní i profesní růst (soft skills, hard skills, jazykové kurzy) </p>  <p>• parkování hned u našich budov </p>  <p>• stravenky ve výši 100 Kč Cafeterie – individuální benefitní systém, kde si každý nastaví své benefit na míru ve výši 12.000 Kč/rok (lze využít na penzijní/životní pojištění, dovolenou, ve zdravotnictví, na volnočasové kativity Vás a Vašich dětí aj.)</p>']],
    ['nazev' => 'CNC Frézař Junior/Senior','kategorie' => [1,3],'mistoVykonu' => 'Plzeň','vzdelani' =>3,'popisPozice' => '<p>• obsluha CNC frézovacího centra - Deckel-Maho, Hermle (řídící systém HEIDENHEIN 530) </p>  <p>• korekce, nastavení a úprava frézovacího centra dle přiložených NC programů </p>  <p>• průběžné a kontrolní měření výrobků </p>  <p>• běžná provozní údržba stroje </p>  <p>• 3-směnný provoz</p>','pozadujeme' => ['<p>• středoškolské nebo střední odborné (obráběč kovů, obráběč CNC, frézař CNC) nebo jiné střední odborné se zkušenostmi a praxí na CNC strojích </p>  <p>• uživatelská znalost práce na PC </p>  <p>• znalost čtení výkresové dokumentace </p>  <p>• znalost práce s měřidly </p>  <p>• praxi v CNC frézování ve strojírenství, (portálová frézka nebo horizontka výhodou) </p>  <p>• praxe s upínáním rozměrnějších součástí pomocí jeřábu </p>  <p>• časová flexibilita – práce na vícesměnný provoz </p>  <p>• samostatnost, pečlivost, spolehlivost, chuť do práce </p>  <p>• znalost řídícího systému HEIDENHEIN 530 výhodou</p>'],'nabizime' => ['<p>• pružnou pracovní dobu </p>  <p>• 5 týdnů dovolené a vybírání náhradního volna je samozřejmost </p>  <p>• osobnostní i profesní růst (soft skills, hard skills, jazykové kurzy) </p>  <p>• parkování hned u našich budov </p>  <p>• stravenky ve výši 100 Kč Cafeterie - individuální benefitní systém, kde si každý nastaví své benefit na míru ve výši 12.000 Kč/rok (lze využít na penzijní/životní pojištění, dovolenou, ve zdravotnictví, na volnočasové kativity Vás a Vašich dětí aj.)</p>']],
    ['nazev' => 'Modelář/ka (GFK/CFK)','kategorie' => [1,3],'mistoVykonu' => 'Plzeň','vzdelani' =>5,'popisPozice' => '<p>Jste zanícený hobby modelář a rád dáváte vozidlům správný tvar a formu? </p>  <p>Chcete být součástí procesu, kde výkresy a konstrukční data nabývají skutečných podob? </p>  <p>Chcete mít možnost oživit návrhy pro výrobu? </p>  <p>Chcete si rozšířit Vaše znalosti s výrobní technikou CFK / GFK? </p>  <p>Tak hledáme právě Vás! </p>  <p>• Stavba komponentů a modelů pro designové studie </p>  <p>• Stavba předváděcích vozů a ergonomických modelů </p>  <p>• Stavba a dokončování hliněných modelů </p>  <p>• Stavba polotovarů, dokončování komponentů, modelů a nástrojů </p>  <p>• Výroba jednotlivých dílů a malosériová výroba v konstrukci prototypů </p>  <p>• Výroba laminátových dílů GFK/CFK</p>','pozadujeme' => ['<p>• SŠ s maturitou nebo vyučen v oboru modelář apod. </p>  <p>• Znalosti v oblastech GFK/CFK, vytvrditelných modelů, povrchových úprav a měření </p>  <p>• Dobrá znalost konvenčního a řízeného zpracování kovů a plastů (např. vrtání, řezání, frézování, soustružení atd.) </p>  <p>• Zájem o práci s tvary a povrchy, motivaci k realizaci vlastních nápadů v týmu </p>  <p>• Cit pro detail, smysl pro preciznost, pečlivost</p>'],'nabizime' => ['<p>• Předáme Vám unikátní know-how, podpořené léty zkušeností </p>  <p>• Poskytneme kompletní zázemí pro zaškolení </p>  <p>• 5 týdnů dovolené a vybírání náhradního volna je samozřejmostí </p>  <p>• Dáme Vám prostor rozvíjet se v mnoha oblastech jak osobnostně, tak profesně </p>  <p>• Talent umíme ocenit, osobnostní i profesní růst Vám zaručíme </p>  <p>• Cafeterie - individuální benefitní systém, kde si každý nastaví své benefity na míru ve výši 12.000 Kč/rok (lze využít na penzijní/životní pojištění, dovolenou, ve zdravotnictví, na volnočasové aktivity aj.)</p>']],
    ['nazev' => 'Zkušební řidič/ka (DPP/DPČ)','kategorie' => [1,3],'mistoVykonu' => 'Plzeň','vzdelani' =>4,'popisPozice' => '<p>• jízda testovacími vozy na předepsané trase </p>  <p>• sběr informací o fungování testovacích vozů za provozu z pohledu řidiče </p>  <p>• podávání zpráv o funkčnosti vozů </p>  <p>• samostatné plánování směn dle Vašich možností</p>','pozadujeme' => ['<p>• řidičský průkaz sk. B minimálně 7 let je podmínkou! </p>  <p>• čistý trestní rejstřík, </p>  <p>• čisté bodové konto řidiče (nutno doložit u pohovoru) </p>  <p>• potvrzení o negativním výsledku testu na Covid 19, který není starší 7 dní! </p>  <p>• logické myšlení + schopnost samostatného a rychlého rozhodování</p>'],'nabizime' => ['<p>• práci na DPP/DPČ s nástupem v průběhu roku 2020 </p>  <p>• odměnu 120-133 Kč / hod. dle počtu opracovaných hodin v měsíci </p>  <p>• alespoň 2 směny týdně, maximálně 6 směn týdně (směny si plánujete dle svých možností v čase 6-14, 14-22, 22-6) </p>  <p>• jedinečnou příležitost řídit luxusní prototypové vozy a podílet se na vývoji nových technologií </p>  <p>• práci bez stresu</p>']],
    ['nazev' => 'Vedoucí projektů ve výrobě','kategorie' => [3,4],'mistoVykonu' => 'Plzeń','vzdelani' =>5,'popisPozice' => '<p>• zajištění výrobních podkladů a dat </p>  <p>• komunikace se zákazníkem a s dodavateli </p>  <p>• plánování termínů zakázky, stanovení struktury projektu, koordinace účastníků projektu </p>  <p>• organizační podpora v dílně a u dodavatelů </p>  <p>• provádění kontroly termínů a nákladů, příprava  informací pro kalkulace </p>  <p>• logistická činnost, příprava odesílacích dokumentů</p>','pozadujeme' => ['<p>• ÚSO vzdělání (možno bez vyučení) </p>  <p>• němčina nebo angličtina na komunikativní úrovni </p>  <p>• znalost technické dokumentace včetně důležitých norem </p>  <p>• obecná znalost hutních materiálů </p>  <p>• znalost MS Office </p>  <p>• základy konstruování CAD </p>  <p>• ŘP skupiny B</p>'],'nabizime' => ['<p>• Předáme Vám unikátní know-how, podpořené léty zkušeností </p>  <p>• Poskytneme kompletní zázemí pro zaškolení </p>  <p>• 5 týdnů dovolené a vybírání náhradního volna je samozřejmostí </p>  <p>• Dáme Vám prostor rozvíjet se v mnoha oblastech jak osobnostně, tak profesně </p>  <p>• Talent umíme ocenit, osobnostní i profesní růst Vám zaručíme </p>  <p>• Cafeterie - individuální benefitní systém, kde si každý nastaví své benefity na míru ve výši 12.000 Kč/rok (lze využít na penzijní/životní pojištění, dovolenou, ve zdravotnictví, na volnočasové aktivity aj.)</p>']],
    
   ]


?>

    <div id="pracovni-pozice">
       <?php
       
        
        include Configuration::componentControler()['templates']."presenter-job/template.php";
        ?>
    </div>