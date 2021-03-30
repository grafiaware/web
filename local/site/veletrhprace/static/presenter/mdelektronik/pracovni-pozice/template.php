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
    [
        'nazev' => 'Operátor výroby',
        'kategorie' => [1,3],
        'mistoVykonu' => 'Rakovník',
        'vzdelani' => 1,
        'popisPozice' => 'Práce v čistém a nehlučném prostředí. Montáž klimatizačních jednotek a kontrolních panelú do aut.',
        'pozadujeme' => [
            'základní vzdělání',
            'manuální zručnost',
            'ochotu učit se novým věcem',
            'spolehlivost',
        ],
        'nabizime' => [
            'nástupní mzdu 23500 Kč, průměrnou mzdu s příplatky za směny až 30000 Kč měsíčně',
            '6 týdnů dovolené',
            'Měsíční a pololetní bonus',
            'Podporu mobility (náhrada výdajů na dojíždění)',
            'Příspěvek na penzijní připojištění 5 % z hrubé mzdy',
            'Dotovanou kantýnu',
            'Příspěvek na volnočasové aktivity ve formě poukázek FlexiPass',
            'Svozovou dopravu zdarma',
        ]
    ],
]


?>

    <div id="pracovni-pozice">
       <?php
       
        
        include Configuration::componentControler()['templates']."presenter-job/template.php";
        ?>
    </div>