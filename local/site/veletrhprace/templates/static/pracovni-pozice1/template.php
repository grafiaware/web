<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
$kategorie = [
    1 => 'výroba/dělnická',
    2 => 'administrativa/THP',
    3 => 'technická',
    4 => 'manažerská/vedoucí'
];
$vzdelani = [
    1 => 'Bez omezení',
    2 => 'ZŠ',
    3 => 'SOU bez maturity',
    4 => 'SOU s maturitou',
    5 => 'SŠ',
    5 => 'VOŠ / Bc.',
    5 => 'VŠ',
];
//template:
//    $kategorie[$pracovniPozice['kategorie1']]
        
        
$pracovniPozice = [
    [
        'nazev' => 'Operátor výroby',
        'kategorie1' => $kategorie[1],
        'kategorie2' => '',
        'mistoVykonu' => 'Rakovník',
        'vzdelani' => $vzdelani[1],
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
    [
        'nazev' => 'Operátor výroby',
        'kategorie1' => $kategorie[1],
        'kategorie2' => $kategorie[3],
        'mistoVykonu' => 'Humpolec',
        'vzdelani' => $vzdelani[1],
        'popisPozice' => 'Práce v čistém a nehlučném prostředí. Montáž kompresorů klimatizace do aut.',
        'pozadujeme' => [
            'základní vzdělání',
            'manuální zručnost',
            'ochotu učit se novým věcem',
            'spolehlivost',
        ],
        'nabizime' => [
            'nástupní mzdu 20500 Kč, průměrnou mzdu s příplatky za směny až 25500 Kč měsíčně',
            '6 týdnů dovolené',
            'Měsíční a pololetní bonus',
            'Příspěvek na penzijní připojištění 5 % z hrubé mzdy',
            'Dotovanou kantýnu',
            'Příspěvek na volnočasové aktivity ve formě poukázek FlexiPass',
            'Svozovou dopravu zdarma',
        ]
    ],
    [
        'nazev' => 'Systémový inženýr - produkty pro elektromobily',
        'kategorie1' => $kategorie[3],
        'kategorie2' => '',
        'mistoVykonu' => 'Praha',
        'vzdelani' => $vzdelani[5],
        'popisPozice' => 'Ve vývojovém centru Valeo v Praze navrhujeme klimatizační jednotky do aut již od roku 2002.
                          V roce 2020 vznikl v Praze nový vývojový tým zabývající se vysokonapěťovými komponenty do klimatizací elektromobilů a do systému chlazení jejich baterií. Tito mechaničtí, testovací a systémoví inženýři posílají své vyvinuté díly do 28 výrobních závodů po celém světě. Blíže pak spolupracují s vývojovým centrem ve francouzském La Verrière. Své projekty si řídí sami a vzhledem k inovativnosti jejich produktů jsou průkopníky na trhu elektromobility.',
        'pozadujeme' => [
            'vysokoškolské vzdělání v oblasti elektroniky',
            'zkušenost se systémovým inženýrstvím nebo s vývojem elektronických součástek',
            'angličtinu na komunikativní úrovni',
        ],
        'nabizime' => [
            '6 týdnů dovolené',
            'Možnost home-office',
            'Bonusy závislé na výsledcích týmu',
            'Podporu mobility (náhrada výdajů na dojíždění, stěhování, dočasné ubytování)',
            'Příspěvek na penzijní připojištění 5 % z hrubé mzdy',
            'Stravenky a výbornou kantýnu',
            'Příspěvek na volnočasové aktivity ve formě poukázek FlexiPass nebo Multisport karty',
            'Technická a jazyková školení',
        ]
    ]
]
?>

<article class="paper">
    <section>
        <headline>
            <?php include "headline.php" ?>
        </headline>
<!--        <perex>
            <?php include "perex.php" ?>
        </perex>-->
    </section>
    <section>    
        <content>
            <?php include "content/vypis-pozic.php" ?>
        </content>
    </section>
</article>
