<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

$headline = 'Timeline';

$timeline1a2 = [
    [
        'zarazeni' => 'Přednáška',
        'boxClass' => 'box-left',
        'icona' => 'chalkboard teacher icon',
        'casOD' => '8:15',
        'casDO' => '9:00',
        'nazev' => 'Na co dát pozor při uzavírání pracovní smlouvy?',
        'firma' => 'Nějaká firma'
    ],
    [
        'zarazeni' => 'Pohovor',
        'boxClass' => 'box-right',
        'icona' => 'microphone icon',
        'casOD' => '10:00',
        'casDO' => '10:20',
        'nazev' => 'Operátor do výroby',
        'firma' => 'Valeo'
    ],
    [
        'zarazeni' => 'Pohovor',
        'boxClass' => 'box-left',
        'icona' => 'microphone icon',
        'casOD' => '10:30',
        'casDO' => '11:00',
        'nazev' => 'Systémový inženýr - produkty pro elektromobily',
        'firma' => 'Valeo'
    ],
    [
        'zarazeni' => 'Přednáška',
        'boxClass' => 'box-right',
        'icona' => 'chalkboard teacher icon',
        'casOD' => '13:15',
        'casDO' => '14:15',
        'nazev' => 'Brýle – pracovní pomůcka i módní doplněk',
        'firma' => 'Optik Švarc'
    ],
    [
        'zarazeni' => 'Přednáška',
        'boxClass' => 'box-left',
        'icona' => 'chalkboard teacher icon',
        'casOD' => '14:20',
        'casDO' => '15:00',
        'nazev' => 'Zvolená rekvalifikace zdarma – cesta k nové profesi',
        'firma' => 'Grafia'
    ]
];

$timeline3 =  [
    [
        'casoveZarazeni' => '8:00',
        'box' => [
            [
                'zarazeni' => 'Přednáška',
                'icona' => 'chalkboard teacher icon',
                'casOD' => '8:15',
                'casDO' => '9:00',
                'nazev' => 'Na co dát pozor při uzavírání pracovní smlouvy?',
                'firma' => 'Nějaká firma'
            ]

        ]
    ],
    [
        'casoveZarazeni' => '10:00',
        'box' => [
            [
                'zarazeni' => 'Pohovor',
                'icona' => 'microphone icon',
                'casOD' => '10:00',
                'casDO' => '10:20',
                'nazev' => 'Operátor do výroby',
                'firma' => 'Valeo'
            ],
            [
                'zarazeni' => 'Pohovor',
                'icona' => 'microphone icon',
                'casOD' => '10:30',
                'casDO' => '11:00',
                'nazev' => 'Systémový inženýr - produkty pro elektromobily',
                'firma' => 'Valeo'
            ],
        ]
    ],
    [
        'casoveZarazeni' => '13:00',
        'box' => [
            [
                'zarazeni' => 'Přednáška',
                'icona' => 'chalkboard teacher icon',
                'casOD' => '13:15',
                'casDO' => '14:15',
                'nazev' => 'Brýle – pracovní pomůcka i módní doplněk',
                'firma' => 'Optik Švarc'
            ],

        ]
    ],
    [
        'casoveZarazeni' => '14:00',
        'box' => [
            [
                'zarazeni' => 'Přednáška',
                'icona' => 'chalkboard teacher icon',
                'casOD' => '14:20',
                'casDO' => '15:00',
                'nazev' => 'Zvolená rekvalifikace zdarma – cesta k nové profesi',
                'firma' => 'Grafia'
            ]  
        ]
    ]
]
    


?>

<article class="paper">
    <section>
        <headline>
            <?php include "headline.php" ?>
        </headline>
        <perex>
            <?php include "perex.php" ?>
        </perex>
    </section>
    <section>    
        <content>
            <?php include "content/harmonogram1.php" ?>
            <?php include "content/harmonogram2.php" ?>
            <?php include "content/harmonogram3.php" ?>
        </content>
    </section>
</article>