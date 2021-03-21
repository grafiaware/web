<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

    $odkazPrednaskyAttributes = ['class' => 'ui large blue button'];
    $odkazPrednaskyTextPrihlasitSe = 'Zde se budete moci přihlásit';
    $odkazPrednaskyTextZhlednout = 'Zhlédnout záznam';

    $polozka = [
        "Kariérové poradenství" => [
            'nazevPrednasky' => "Kariérové poradenství",
            'perex' => "Změna profese, jaké profesní kurzy a rekvalifikace zvolit s ohledem na trh práce v PK, \"kontrola životopisů\"…",
            'instituce' => "Grafia"],
        "Pracovně-právní poradna" => [
            'nazevPrednasky' => "Pracovně-právní poradna",
            'perex' => "Pracovní právník radí českým i zahraničním zaměstnancům",
            'instituce' => "Státní úřad inspekce práce"],
        "Poradna pro začínající podnikatele" => [
            'nazevPrednasky' => "Poradna pro začínající podnikatele",
            'perex' => "Jak založit skvělý start up? Co všechno potřebujete, než začnete podnikat? Chcete se vyhnout problémům?" ,
            'instituce' => "CzechInvest"],
        "Jak se nespálit v zahraničí" => [
            'nazevPrednasky' => "Jak se nespálit v zahraničí",
            'perex' => "Potřebujete poradit se všemi náležitostmi práce mimo ČR?",
            'instituce' => "EURES"],
        "Poradna pro cizince pracující v ČR" => [
            'nazevPrednasky' => "Poradna pro cizince pracující v ČR",
            'perex' => "",
            'instituce' => "Diakonie Západ"],
        "Poradna první  psychologické pomoci" => [
            'nazevPrednasky' => "Poradna první  psychologické pomoci",
            'perex' => "",
            'instituce' => "Diakonie Západ"],
        "Poradna v těžkých životních situacích (občanská poradna)" => [
            'nazevPrednasky' => "Poradna v těžkých životních situacích (občanská poradna)",
            'perex' => "",
            'instituce' => "Diakonie Západ"],
        ];

    $prednaska = [
        [
            'den' => 1,
            'datum' => '30. 3. 2021',
            'prednasky' => [
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'casOd' => '10:00',
                    'casDo' => '12:00',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ] + $polozka["Kariérové poradenství"],
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'casOd' => '12:30',
                    'casDo' => '18:00',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ] + $polozka["Kariérové poradenství"],
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'casOd' => '14:00',
                    'casDo' => '15:50',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ] + $polozka["Pracovně-právní poradna"],
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'casOd' => '10:00',
                    'casDo' => '12:00',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ] + $polozka["Poradna pro začínající podnikatele"],
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'casOd' => '9:00',
                    'casDo' => '12:00',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ] + $polozka["Poradna pro cizince pracující v ČR"],
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'casOd' => '13:00',
                    'casDo' => '17:00',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ] + $polozka["Poradna pro cizince pracující v ČR"],
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'casOd' => '9:00',
                    'casDo' => '12:00',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ] + $polozka["Poradna v těžkých životních situacích (občanská poradna)"],
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'casOd' => '13:00',
                    'casDo' => '16:00',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ] + $polozka["Poradna v těžkých životních situacích (občanská poradna)"],            ]
        ],
        [
            'den' => 2,
            'datum' => '31. 3. 2021',
            'prednasky' => [
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'casOd' => '10:00',
                    'casDo' => '14:00',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ] + $polozka["Kariérové poradenství"],
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'casOd' => '14:00',
                    'casDo' => '16:50',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ] + $polozka["Pracovně-právní poradna"],
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'casOd' => '13:00',
                    'casDo' => '14:45',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ] + $polozka["Poradna pro začínající podnikatele"],
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'casOd' => '15:00',
                    'casDo' => '17:00',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ] + $polozka["Jak se nespálit v zahraničí"],
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'casOd' => '9:00',
                    'casDo' => '16:00',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ] + $polozka["Poradna první  psychologické pomoci"],
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'casOd' => '9:00',
                    'casDo' => '12:00',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ] + $polozka["Poradna v těžkých životních situacích (občanská poradna)"],
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'casOd' => '13:00',
                    'casDo' => '16:00',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ] + $polozka["Poradna v těžkých životních situacích (občanská poradna)"],
            ],
        ],
        [
            'den' => 3,
            'datum' => '1. 4. 2021',
            'prednasky' => [
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'casOd' => '10:00',
                    'casDo' => '12:00',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ] + $polozka["Kariérové poradenství"],
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'casOd' => '12:30',
                    'casDo' => '16:00',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ] + $polozka["Kariérové poradenství"],
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'casOd' => '14:00',
                    'casDo' => '15:50',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ] + $polozka["Pracovně-právní poradna"],
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'casOd' => '10:00',
                    'casDo' => '12:00',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ] + $polozka["Jak se nespálit v zahraničí"],
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'casOd' => '9:00',
                    'casDo' => '13:00',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ] + $polozka["Poradna pro cizince pracující v ČR"],
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'casOd' => '9:00',
                    'casDo' => '12:00',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ] + $polozka["Poradna v těžkých životních situacích (občanská poradna)"],
            ],
        ]
    ]
;