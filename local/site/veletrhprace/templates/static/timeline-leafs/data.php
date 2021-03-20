<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

$headline = 'Timeline';

$linkButtonAttributes = ['class' => 'ui middle red button'];
$linkButtonTextPrihlasitSe = 'Zde se budete moci přihlásit';

$eventType = [
    'Přednáška' => ['name'=>'Přednáška', 'icon'=>'chalkboard teacher icon'],
    'Pohovor'=> ['name'=>'Pohovor', 'icon'=> 'microphone icon'],
    'Poradna' => ['name'=>'Poradna', 'icon'=> 'user friends icon'],
];

$event=  [
    [
        'timelinePoint' => '8:00',
        'box' => [
            [
                'published' => '1',
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'startTime' => '8:15',
                'endTime' => '9:00',
                'linkButton' => [
                                'show' => 1,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'title' => 'Na co dát pozor při uzavírání pracovní smlouvy?',
                'perex' => 'Výpovědi, překážky v práci, náhrada mzdy, náležitosti pracovní smlouvy…',
                'institution' => ['type'=>'', 'name'=>'SÚIP'],
                'party' => 'Petra Součková',
            ]

        ]
    ],
    [
        'timelinePoint' => '10:00',
        'box' => [
            [
                'published' => '1',
                'boxClass' => 'box-left',
                'eventType' => $eventType['Pohovor'],
                'startTime' => '10:00',
                'endTime' => '10:20',
                'linkButton' => [
                                'show' => 1,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'title' => 'Operátor do výroby',
                'perex' => 'Výpovědi, překážky v práci, náhrada mzdy, náležitosti pracovní smlouvy…',
                'institution' => ['type'=>'', 'name'=>'Valeo'],
                'party' => 'Petra Součková',
            ],
            [
                'published' => '1',
                'boxClass' => 'box-right',
                'eventType' => $eventType['Pohovor'],
                'startTime' => '10:30',
                'endTime' => '11:00',
                'linkButton' => [
                                'show' => 1,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'title' => 'Systémový inženýr - produkty pro elektromobily',
                'perex' => '',
                'institution' => ['type'=>'', 'name'=>'Valeo'],
                'party' => '',
            ],
        ]
    ],
    [
        'timelinePoint' => '13:00',
        'box' => [
            [
                'published' => '1',
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'startTime' => '13:15',
                'endTime' => '14:15',
                'linkButton' => [
                                'show' => 1,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'title' => 'Brýle – pracovní pomůcka i módní doplněk',
                'perex' => '',
                'institution' => ['type'=>'', 'name'=>'Optik Švarc'],
                'party' => '',
            ],
            [
                'published' => '1',
                'boxClass' => 'box-right',
                'eventType' => $eventType['Poradna'],
                'startTime' => '13:15',
                'endTime' => '14:15',
                'linkButton' => [
                                'show' => 1,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'title' => 'Brýle – pracovní pomůcka i módní doplněk',
                'perex' => '',
                'institution' => ['type'=>'', 'name'=>'Optik Švarc'],
                'party' => '',
            ],
        ]
    ],
    [
        'timelinePoint' => '14:00',
        'box' => [
            [
                'published' => '1',
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'startTime' => '14:20',
                'endTime' => '15:00',
                'linkButton' => [
                                'show' => 1,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'title' => 'Zvolená rekvalifikace zdarma – cesta k nové profesi',
                'perex' => '',
                'institution' => ['type'=>'', 'name'=>'Grafia'],
                'party' => '',
            ]
        ]
    ]
];


