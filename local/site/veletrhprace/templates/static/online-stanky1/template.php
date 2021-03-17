<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

$static_ref = '_www_vp_files/static/';
$logo_ref = '/assets/logo.png'; 

$headline = 'Online stánky';
$perex = 'Poznejte svého budoucího zaměstnavatele na našich online stáncích.';
$vystavovatele = [
        [
            'radekVystavovatelu' => [
                [
                    'nazevVystavovatele' => 'Grafia s. r. o.',
                    'stanekVystavovateleOdkaz' => '',
                    'infoProPrihlasene' => [
                        [
                            'popis' => 'Tuto firmu jste si již prohlédli',
                            'ikona' => 'history',
                        ],
                        [
                            'popis' => 'Individuální pohovor <br/> <b>ve čtvrtek 18. 3. v 10:00</b>',
                            'ikona' => 'calendar alternate',
                        ],
                        [
                            'popis' => 'Leták v <a>igelitce!</a>',
                            'ikona' => 'file alternate',
                        ],
                    ],
                    'imgVystavovateleAttributes' => [
                        'src' => $static_ref.'grafia'.$logo_ref,
                        'alt' => 'Firma Grafia',
                        'width' => '',
                    ]
                ],
                [
                    'nazevVystavovatele' => 'Daikin',
                    'stanekVystavovateleOdkaz' => '',
                    'infoProPrihlasene' => [
                        [
                            'popis' => '',
                            'ikona' => '',
                        ],
                        [
                            'popis' => '',
                            'ikona' => '',
                        ],
                    ],
                    'imgVystavovateleAttributes' => [
                        'src' => $static_ref.'daikin'.$logo_ref,
                        'alt' => 'Firma Daikin',
                        'width' => '',
                    ]
                ],
            ]
        ],
        [
            'radekVystavovatelu' => [
                [
                    'nazevVystavovatele' => 'Konplan',
                    'stanekVystavovateleOdkaz' => '',
                    'infoProPrihlasene' => [
                        [
                            'popis' => 'Tuto firmu jste si již prohlédli',
                            'ikona' => 'history',
                        ],
                        [
                            'popis' => 'Individuální pohovor <br/> <b>ve čtvrtek 18. 3. v 10:00</b>',
                            'ikona' => 'calendar alternate',
                        ],
                        [
                            'popis' => 'Leták v <a>igelitce!</a>',
                            'ikona' => 'file alternate',
                        ],
                    ],
                    'imgVystavovateleAttributes' => [
                        'src' => $static_ref.'konplan'.$logo_ref,
                        'alt' => 'Firma Konplan',
                        'width' => '',
                    ]
                ],
                [
                    'nazevVystavovatele' => 'MD Elektronik',
                    'stanekVystavovateleOdkaz' => '',
                    'infoProPrihlasene' => [
                        [
                            'popis' => '',
                            'ikona' => '',
                        ],
                        [
                            'popis' => '',
                            'ikona' => '',
                        ],
                    ],
                    'imgVystavovateleAttributes' => [
                        'src' => '', //$static_ref.'mdelektronik'.$logo_ref
                        'alt' => '',
                        'width' => '',
                    ]
                ],
            ]
        ],
        [
            'radekVystavovatelu' => [
                [
                    'nazevVystavovatele' => 'Úřad práce',
                    'stanekVystavovateleOdkaz' => '',
                    'infoProPrihlasene' => [
                        [
                            'popis' => 'Tuto firmu jste si již prohlédli',
                            'ikona' => 'history',
                        ],
                        [
                            'popis' => 'Individuální pohovor <br/> <b>ve čtvrtek 18. 3. v 10:00</b>',
                            'ikona' => 'calendar alternate',
                        ],
                        [
                            'popis' => 'Leták v igelitce!',
                            'ikona' => 'file alternate',
                        ],
                    ],
                    'imgVystavovateleAttributes' => [
                        'src' => $static_ref.'up'.$logo_ref,
                        'alt' => 'ÚP',
                        'width' => '',
                    ]
                ],
                [
                    'nazevVystavovatele' => 'Stoezle',
                    'stanekVystavovateleOdkaz' => '',
                    'infoProPrihlasene' => [
                        [
                            'popis' => '',
                            'ikona' => '',
                        ],
                        [
                            'popis' => '',
                            'ikona' => '',
                        ],
                    ],
                    'imgVystavovateleAttributes' => [
                        'src' => $static_ref.'stoezle'.$logo_ref,
                        'alt' => '',
                        'width' => '',
                    ]
                ],
            ]
        ],
        [
            'radekVystavovatelu' => [
                [
                    'nazevVystavovatele' => 'Wienerberger',
                    'stanekVystavovateleOdkaz' => '',
                    'infoProPrihlasene' => [
                        [
                            'popis' => 'Tuto firmu jste si již prohlédli',
                            'ikona' => 'history',
                        ],
                        [
                            'popis' => 'Individuální pohovor <br/> <b>ve čtvrtek 18. 3. v 10:00</b>',
                            'ikona' => 'calendar alternate',
                        ],
                        [
                            'popis' => 'Leták v igelitce!',
                            'ikona' => 'file alternate',
                        ],
                    ],
                    'imgVystavovateleAttributes' => [
                        'src' => $static_ref.'wienerberger'.$logo_ref,
                        'alt' => 'Firma Kermi',
                        'width' => '',
                    ]
                ],
                [
                    'nazevVystavovatele' => 'Akka',
                    'stanekVystavovateleOdkaz' => '',
                    'infoProPrihlasene' => [
                        [
                            'popis' => '',
                            'ikona' => '',
                        ],
                        [
                            'popis' => '',
                            'ikona' => '',
                        ],
                    ],
                    'imgVystavovateleAttributes' => [
                        'src' => '', //$static_ref.'akka'.$logo_ref
                        'alt' => '',
                        'width' => '',
                    ]
                ],
            ]
        ],
        [
            'radekVystavovatelu' => [
                [
                    'nazevVystavovatele' => 'Kermi',
                    'stanekVystavovateleOdkaz' => '',
                    'infoProPrihlasene' => [
                        [
                            'popis' => 'Tuto firmu jste si již prohlédli',
                            'ikona' => 'history',
                        ],
                        [
                            'popis' => 'Individuální pohovor <br/> <b>ve čtvrtek 18. 3. v 10:00</b>',
                            'ikona' => 'calendar alternate',
                        ],
                        [
                            'popis' => 'Leták v igelitce!',
                            'ikona' => 'file alternate',
                        ],
                    ],
                    'imgVystavovateleAttributes' => [
                        'src' => $static_ref.'kermi'.$logo_ref,
                        'alt' => 'Firma Kermi',
                        'width' => '',
                    ]
                ],
                [
                    'nazevVystavovatele' => 'Drůběžářský závod Klatovy',
                    'stanekVystavovateleOdkaz' => '',
                    'infoProPrihlasene' => [
                        [
                            'popis' => '',
                            'ikona' => '',
                        ],
                        [
                            'popis' => '',
                            'ikona' => '',
                        ],
                    ],
                    'imgVystavovateleAttributes' => [
                        'src' => $static_ref.'dzk'.$logo_ref,
                        'alt' => '',
                        'width' => '',
                    ]
                ],
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
    <?php include "content/hala.php" ?>
</article>