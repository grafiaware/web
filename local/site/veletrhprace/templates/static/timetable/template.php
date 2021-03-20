<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

$headline = 'Timeline';

$eventType = [
    'Přednáška' => ['name'=>'Přednáška', 'icon'=>'chalkboard teacher icon'],
    'Pohovor'=> ['name'=>'Pohovor', 'icon'=> 'microphone icon'],
    'Poradna' => ['name'=>'Poradna', 'icon'=> 'user friends icon'],
];


$timeline1a2 = [
    [
        'eventType' => 'Přednáška',
        'boxClass' => 'box-left',
        'icon' => 'chalkboard teacher icon',
        'startTime' => '8:15',
        'endTime' => '9:00',
        'name' => 'Na co dát pozor při uzavírání pracovní smlouvy?',
        'institution' => 'Nějaká institution'
    ],
    [
        'eventType' => 'Pohovor',
        'boxClass' => 'box-right',
        'icon' => 'microphone icon',
        'startTime' => '10:00',
        'endTime' => '10:20',
        'name' => 'Operátor do výroby',
        'institution' => 'Valeo'
    ],
    [
        'eventType' => 'Pohovor',
        'boxClass' => 'box-left',
        'icon' => 'microphone icon',
        'startTime' => '10:30',
        'endTime' => '11:00',
        'name' => 'Systémový inženýr - produkty pro elektromobily',
        'institution' => 'Valeo'
    ],
    [
        'eventType' => 'Přednáška',
        'boxClass' => 'box-right',
        'icon' => 'chalkboard teacher icon',
        'startTime' => '13:15',
        'endTime' => '14:15',
        'name' => 'Brýle – pracovní pomůcka i módní doplněk',
        'institution' => 'Optik Švarc'
    ],
    [
        'eventType' => 'Přednáška',
        'boxClass' => 'box-left',
        'icon' => 'chalkboard teacher icon',
        'startTime' => '14:20',
        'endTime' => '15:00',
        'name' => 'Zvolená rekvalifikace zdarma – cesta k nové profesi',
        'institution' => 'Grafia'
    ]
];

$timeline=  [
    [
        'timelinePoint' => '8:00',
        'box' => [
            [
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'startTime' => '8:15',
                'endTime' => '9:00',
                'title' => 'Na co dát pozor při uzavírání pracovní smlouvy?',
                'institution' => ['type'=>'', 'name'=>'SÚIP']
            ]

        ]
    ],
    [
        'timelinePoint' => '10:00',
        'box' => [
            [
                'boxClass' => 'box-left',
                'eventType' => $eventType['Pohovor'],
                'startTime' => '10:00',
                'endTime' => '10:20',
                'title' => 'Operátor do výroby',
                'institution' => ['type'=>'', 'name'=>'Valeo']

            ],
            [
                'boxClass' => 'box-right',
                'eventType' => $eventType['Pohovor'],
                'startTime' => '10:30',
                'endTime' => '11:00',
                'title' => 'Systémový inženýr - produkty pro elektromobily',
                'institution' => ['type'=>'', 'name'=>'Valeo']
            ],
        ]
    ],
    [
        'timelinePoint' => '13:00',
        'box' => [
            [
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'startTime' => '13:15',
                'endTime' => '14:15',
                'title' => 'Brýle – pracovní pomůcka i módní doplněk',
                'institution' => ['type'=>'', 'name'=>'Optik Švarc']
            ],
            [
                'boxClass' => 'box-right',
                'eventType' => $eventType['Poradna'],
                'startTime' => '13:15',
                'endTime' => '14:15',
                'title' => 'Brýle – pracovní pomůcka i módní doplněk',
                'institution' => ['type'=>'', 'name'=>'Optik Švarc']
            ],
        ]
    ],
    [
        'timelinePoint' => '14:00',
        'box' => [
            [
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'startTime' => '14:20',
                'endTime' => '15:00',
                'title' => 'Zvolená rekvalifikace zdarma – cesta k nové profesi',
                'institution' => ['type'=>'', 'name'=>'Grafia']
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
            <?php include "content/timetable.php" ?>
        </content>
    </section>
</article>