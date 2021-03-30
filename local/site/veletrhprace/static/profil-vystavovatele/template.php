<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Model\Entity\LoginAggregateCredentialsInterface;;
use Model\Repository\EnrollRepo;
use Model\Repository\StatusSecurityRepo;

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


$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurity = $statusSecurityRepo->get();
/** @var LoginAggregateCredentialsInterface $loginAggregate */
$loginAggregate = $statusSecurity->getLoginAggregate();

if (isset($loginAggregate)) {
    $loginName = $loginAggregate->getLoginName();
    $enrollRepo = $container->get(EnrollRepo::class);
    $enrolls = $enrollRepo->findByLoginName($loginName);

    $headline = "Můj profil";
    $perex = $loginAggregate->getLoginName();



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
        <!--<content>-->
            <?php include "content/profil.php" ?> <!-- Tiny pro .working-data -->
        <!--</content>-->
    </section>
</article>