<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

$static_ref = '_www_vp_files/presenter/';
$logo_ref = '/assets/logo.png';

$headline = 'Online stánky';
$perex = 'Poznejte svého budoucího zaměstnavatele na našich online stáncích.';

$infoForRegistered = [
        [
            'description' => 'Tuto firmu jste si již prohlédli',
            'icon' => 'history',
        ],
        [
            'description' => 'Individuální pohovor <br/> <b>ve čtvrtek 18. 3. v 10:00</b>',
            'icon' => 'calendar alternate',
        ],
        [
            'description' => 'Leták v <a>igelitce!</a>',
            'icon' => 'file alternate',
        ],
];

$exhibitor = [
        [
            'row' => [
                [
                    'name' => 'Grafia',
                    'urlStand' => 'www/block/grafia',
                    'logoAttributes' => [
                        'src' => $static_ref.'grafia'.$logo_ref,
                        'alt' => 'Firma Grafia',
                    ]
                ],
                [
                    'name' => 'Daikin',
                    'urlStand' => 'www/block/daikin',
                    'logoAttributes' => [
                        'src' => $static_ref.'daikin'.$logo_ref,
                        'alt' => 'Firma Daikin',
                    ]
                ],
            ]
        ],
        [
            'row' => [
                [
                    'name' => 'Konplan',
                    'urlStand' => 'www/block/konplan',
                    'logoAttributes' => [
                        'src' => $static_ref.'konplan'.$logo_ref,
                        'alt' => 'Firma Konplan',
                    ]
                ],
                [
                    'name' => 'MD Elektronik',
                    'urlStand' => 'www/block/mdelektronik',
                    'logoAttributes' => [
                        'src' => $static_ref.'mdelektronik'.$logo_ref,
                        'alt' => 'Firma MD Elektronik',
                    ]
                ],
            ]
        ],
        [
            'row' => [
                [
                    'name' => 'Úřad práce',
                    'urlStand' => 'www/block/up',
                    'logoAttributes' => [
                        'src' => $static_ref.'up'.$logo_ref,
                        'alt' => 'Firma ÚP',
                    ]
                ],
                [
                    'name' => 'Stoelzle',
                    'urlStand' => 'www/block/stoelzle',
                    'logoAttributes' => [
                        'src' => $static_ref.'stoelzle'.$logo_ref,
                        'alt' => 'Firma Stoelzle',
                    ]
                ],
            ]
        ],
        [
            'row' => [
                [
                    'name' => 'Wienerberger',
                    'urlStand' => 'www/block/wienerberger',
                    'logoAttributes' => [
                        'src' => $static_ref.'wienerberger'.$logo_ref,
                        'alt' => 'Firma Wienerberger',
                    ]
                ],
                [
                    'name' => 'Akka',
                    'urlStand' => 'www/block/akka',
                    'logoAttributes' => [
                        'src' => $static_ref.'akka'.$logo_ref,
                        'alt' => 'Firma Akka',
                    ]
                ],
            ]
        ],
        [
            'row' => [
                [
                    'name' => 'Kermi',
                    'urlStand' => 'www/block/kermi',
                    'logoAttributes' => [
                        'src' => $static_ref.'kermi'.$logo_ref,
                        'alt' => 'Firma Kermi',
                    ]
                ],
                [
                    'name' => 'Drůběžářský závod Klatovy',
                    'urlStand' => 'www/block/dzk',
                    'logoAttributes' => [
                        'src' => $static_ref.'dzk'.$logo_ref,
                        'alt' => 'Firma DZK',
                    ]
                ],
            ]
        ],
        [
            'row' => [
                [
                    'name' => 'Possehl',
                    'urlStand' => 'www/block/possehl',
                    'logoAttributes' => [
                        'src' => $static_ref.'possehl'.$logo_ref,
                        'alt' => 'Firma Possehl',
                    ]
                ],
                [
                    'name' => 'Valeo',
                    'urlStand' => 'www/block/valeo',
                    'logoAttributes' => [
                        'src' => $static_ref.'valeo'.$logo_ref,
                        'alt' => 'Firma Valeo',
                    ]
                ],
            ]
        ]
    ];
//            echo    "<pre>".var_dump($exhibitor)."</pre>";
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
    <?php include "content/hala.php" ?>
</article>