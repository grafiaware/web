<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

$osobniUdaje = [
    [
        'fotografie' => [
            'src' => 'images/moje-krasna-fotka.jpg',
            'alt' => 'Profilový obrázek',
            'width' => '',
            'height' => '',
        ],
        'jmeno' => 'Lukáš Novák',
        'email' => 'novak@nereknu.cz',
        'telefon' => '+420 123 456 789',
        'pracPopis' => 'Momentálně bez práce',
        'nahraneSoubory' => [
            'zivotopis' => 'cesta k souboru',
        ],
    ]
];
$harmonogram = [
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
$igelitka = [
    
];

?>
    <div class="profil">
        <div class="ui stackable centered grid">
            <div class="column">
                <div class="ui styled fluid accordion">
                    <?= $this->insert(__DIR__.'/profil/osobni-udaje.php', $osobniUdaje) ?>
                    <?= $this->insert(__DIR__.'/profil/harmonogram.php', $harmonogram) ?>
                    <?= $this->insert(__DIR__.'/profil/igelitka.php', $igelitka) ?>
                </div>
                <br/>
            </div>
        </div>
        <p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll">Harmonogram</p>
        <div class="ui centered grid">
            <div class="two wide column">
                <div class="casova-osa">
                    <p>8</p>
                    <p>9</p>
                    <p>10</p>
                    <p>11</p>
                    <p>12</p>
                    <p>13</p>
                    <p>14</p>
                    <p>15</p>
                    <p>16</p>
                    <p>17</p>
                    <p>18</p>
                </div>
            </div>
            <div class="fourteen wide column">
                <div class="aktivity">
                    <div class="ui raised segment">
                        <p>Přednáška 8:00 - 8:45</p>
                        <p>Název přednášky: Nová přednáška od naší firmy</p>
                        <p>Firma: Nejlepší firma s.r.o.</p>
                    </div>
                    <div class="ui raised segment">
                        <p>Pohovor 8:30 - 8:50</p>
                        <p>Firma: Nejlepší firma s.r.o.</p>
                        <p class="ui right ribbon label">Důležité</p>
                    </div>
                </div>
            </div>
        </div>
    </div>