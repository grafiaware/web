<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$personalData = [
    [
        'fotografie' => [
            'src' => 'images/moje-krasna-fotka.jpg',
            'alt' => 'Profilový obrázek',
            'width' => '',
            'height' => '',
        ],
        'titulPred' => '',
        'titulPO' => '',
        'jmeno' => 'Novák',
        'prijmeni' => 'Novák',
        'email' => 'novak@nereknu.cz',
        'telefon' => '+420 123 456 789',
        'pracPopis' => 'Momentálně bez práce',
        'nahraneSoubory' => [
            'zivotopis' => 'cesta k souboru',
        ],
    ]
];
$timeline = [
    'denKonani' => [
        [
            'datum' => '30. 3. 2021',
            'prednasky' => [
                [
                    'cas' => '10:30',
                    'nazevPrednasky' => 'Naše nejlepší přednáška',
                    'firma' => 'Firma s. r. o.',
                    'odkazNaPrednasku' => ''
                ],
                [
                    'cas' => '12:45',
                    'nazevPrednasky' => 'Nejlepší přednáška na světě',
                    'firma' => 'Firma a. s.',
                    'odkazNaPrednasku' => ''
                ]

            ],
            'pohovory' => [
                [
                    'cas' => '10:30',
                    'firma' => 'Firma s. r. o.',
                    'odkazNaPohovor' => ''
                ],
            ]
        ],
        [
            'datum' => '31. 3. 2021',
            'prednasky' => [
                [
                    'cas' => '13:00',
                    'nazevPrednasky' => 'Naše nejlepší přednáška',
                    'firma' => 'Firma XY s. r. o.',
                    'odkazNaPrednasku' => ''
                ],
                [
                    'cas' => '13:45',
                    'nazevPrednasky' => 'Nejlepší přednáška na světě',
                    'firma' => 'Firma XY a. s.',
                    'odkazNaPrednasku' => ''
                ]

            ],
            'pohovory' => [
                [
                    'cas' => '15:00',
                    'firma' => 'Firma XY s. r. o.',
                    'odkazNaPohovor' => ''
                ],
            ]
        ],
        [
            'datum' => '1. 4. 2021',
            'prednasky' => [
                [
                    'cas' => '13:00',
                    'nazevPrednasky' => 'Naše nejlepší přednáška',
                    'firma' => 'Firma AB s. r. o.',
                    'odkazNaPrednasku' => ''
                ],
                [
                    'cas' => '13:45',
                    'nazevPrednasky' => 'Nejlepší přednáška na světě',
                    'firma' => 'Firma AB a. s.',
                    'odkazNaPrednasku' => ''
                ]

            ],
            'pohovory' => [
                [
                    'cas' => '11:15',
                    'firma' => 'Firma AB s. r. o.',
                    'odkazNaPohovor' => ''
                ],
            ]
        ]
    ]
];
