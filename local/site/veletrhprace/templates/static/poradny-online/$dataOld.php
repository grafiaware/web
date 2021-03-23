<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

$linkButtonAttributes = ['class' => 'ui large red button'];
$linkButtonTextPrihlasitSe = 'Zde se budete moci přihlásit';
$linkButtonTextZhlednout = 'Zhlédnout záznam';

$eventType = [
    'Přednáška' => ['name'=>'Přednáška', 'icon'=>'chalkboard teacher icon'],
    'Pohovor'=> ['name'=>'Pohovor', 'icon'=> 'microphone icon'],
    'Poradna' => ['name'=>'Poradna', 'icon'=> 'user friends icon'],
];

$eventContent = [
    "Kariérové poradenství" => [
        'eventType' => $eventType['Poradna'],
        'title' => "Kariérové poradenství",
        'perex' => "Změna profese, jaké profesní kurzy a rekvalifikace zvolit s ohledem na trh práce v PK, \"kontrola životopisů\"…",
        'institution' => ['type'=>'', 'name'=>'Grafia'],
        'party' => '',
        ],
    "Pracovně-právní poradna" => [
        'eventType' => $eventType['Poradna'],
        'title' => "Pracovně-právní poradna",
        'perex' => "Pracovní právník radí českým i zahraničním zaměstnancům",
        'institution' => ['type'=>'', 'name'=>'Státní úřad inspekce práce'],
        'party' => '',
        ],
    "Poradna pro začínající podnikatele" => [
        'eventType' => $eventType['Poradna'],
        'title' => "Poradna pro začínající podnikatele",
        'perex' => "Jak založit skvělý start up? Co všechno potřebujete, než začnete podnikat? Chcete se vyhnout problémům?" ,
        'institution' => ['type'=>'', 'name'=>'CzechInvest'],
        'party' => '',
        ],
    "Jak se nespálit v zahraničí" => [
        'eventType' => $eventType['Poradna'],
        'title' => "Jak se nespálit v zahraničí",
        'perex' => "Potřebujete poradit se všemi náležitostmi práce mimo ČR?",
        'institution' => ['type'=>'', 'name'=>'EURES'],
        'party' => '',
        ],
    "Jak se nespálit v zahraničí (telefonicky)" => [
        'eventType' => $eventType['Poradna'],
        'title' => "Jak se nespálit v zahraničí",
        'perex' => "Potřebujete poradit se všemi náležitostmi práce mimo ČR? <b>Telefonicky</b> - čísla na poradce <a href=\"https://www.uradprace.cz/web/cz/kontakty-na-eures-poradce\" target=\"_blank\">zde</a>",
        'institution' => ['type'=>'', 'name'=>'EURES'],
        'party' => '',
        ],
    "Poradna pro cizince pracující v ČR" => [
        'eventType' => $eventType['Poradna'],
        'title' => "Poradna pro cizince pracující v ČR",
        'perex' => "",
        'institution' => ['type'=>'', 'name'=>'Diakonie Západ'],
        'party' => '',
        ],
    "Poradna první  psychologické pomoci" => [
        'eventType' => $eventType['Poradna'],
        'title' => "Poradna první  psychologické pomoci",
        'perex' => "",
        'institution' => ['type'=>'', 'name'=>'Diakonie Západ'],
        'party' => '',
        ],
    "Poradna v těžkých životních situacích (občanská poradna)" => [
        'eventType' => $eventType['Poradna'],
        'title' => "Poradna v těžkých životních situacích (občanská poradna)",
        'perex' => "",
        'institution' => ['type'=>'', 'name'=>'Diakonie Západ'],
        'party' => '',
        ],
    ];

$event = [
    [
        'timelinePoint' => '30. 3. 2021',
        'box' => [
            [
                'published' => '1',
                'startTime' => '10:00',
                'endTime' => '12:00',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
            ] + $eventContent["Kariérové poradenství"],
            [
                'published' => '1',
                'startTime' => '12:30',
                'endTime' => '18:00',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
            ] + $eventContent["Kariérové poradenství"],
            [
                'published' => '1',
                'startTime' => '12:00',
                'endTime' => '14:00',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
            ] + $eventContent["Jak se nespálit v zahraničí"],
            [
                'published' => '1',
                'startTime' => '14:00',
                'endTime' => '15:50',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
            ] + $eventContent["Pracovně-právní poradna"],
            [
                'published' => '1',
                'startTime' => '10:00',
                'endTime' => '12:00',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
            ] + $eventContent["Poradna pro začínající podnikatele"],
            [
                'published' => '1',
                'startTime' => '9:00',
                'endTime' => '12:00',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
            ] + $eventContent["Poradna pro cizince pracující v ČR"],
            [
                'published' => '1',
                'startTime' => '13:00',
                'endTime' => '17:00',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
            ] + $eventContent["Poradna pro cizince pracující v ČR"],
            [
                'published' => '1',
                'startTime' => '9:00',
                'endTime' => '12:00',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
            ] + $eventContent["Poradna v těžkých životních situacích (občanská poradna)"],
            [
                'published' => '1',
                'startTime' => '13:00',
                'endTime' => '16:00',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
            ] + $eventContent["Poradna v těžkých životních situacích (občanská poradna)"],            ]
    ],
    [
        'timelinePoint' => '31. 3. 2021',
        'box' => [
            [
                'published' => '1',
                'startTime' => '10:00',
                'endTime' => '14:00',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
            ] + $eventContent["Kariérové poradenství"],
            [
                'published' => '1',
                'startTime' => '14:00',
                'endTime' => '16:50',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
            ] + $eventContent["Pracovně-právní poradna"],
            [
                'published' => '1',
                'startTime' => '13:00',
                'endTime' => '14:45',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
            ] + $eventContent["Poradna pro začínající podnikatele"],
            [
                'published' => '1',
                'startTime' => '15:00',
                'endTime' => '17:00',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
            ] + $eventContent["Jak se nespálit v zahraničí (telefonicky)"],
            [
                'published' => '1',
                'startTime' => '9:00',
                'endTime' => '16:00',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
            ] + $eventContent["Poradna první  psychologické pomoci"],
            [
                'published' => '1',
                'startTime' => '9:00',
                'endTime' => '12:00',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
            ] + $eventContent["Poradna v těžkých životních situacích (občanská poradna)"],
            [
                'published' => '1',
                'startTime' => '13:00',
                'endTime' => '16:00',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
            ] + $eventContent["Poradna v těžkých životních situacích (občanská poradna)"],
        ],
    ],
    [
        'timelinePoint' => '1. 4. 2021',
        'box' => [
            [
                'published' => '1',
                'startTime' => '10:00',
                'endTime' => '12:00',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
            ] + $eventContent["Kariérové poradenství"],
            [
                'published' => '1',
                'startTime' => '12:30',
                'endTime' => '16:00',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
            ] + $eventContent["Kariérové poradenství"],
            [
                'published' => '1',
                'startTime' => '14:00',
                'endTime' => '15:50',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
            ] + $eventContent["Pracovně-právní poradna"],
            [
                'published' => '1',
                'startTime' => '10:00',
                'endTime' => '12:00',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
            ] + $eventContent["Jak se nespálit v zahraničí (telefonicky)"],
            [
                'published' => '1',
                'startTime' => '9:00',
                'endTime' => '13:00',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
            ] + $eventContent["Poradna pro cizince pracující v ČR"],
            [
                'published' => '1',
                'startTime' => '9:00',
                'endTime' => '12:00',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
            ] + $eventContent["Poradna v těžkých životních situacích (občanská poradna)"],

        ],
    ]
]
;