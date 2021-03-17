<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
$static_ref = '_www_vp_files/static/';
$logo_ref = '/assets/logo.png';

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
                            'popis' => 'Leták v igelitce!',
                            'ikona' => 'file alternate',
                        ],
                    ],
                    'imgVystavovateleAttributes' => [
                        'src' => $static_ref.'grafia'.$logo_ref,
                        'alt' => 'Firma Grafia',
                        'width' => '',
                        'height' => '100',
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
                        'height' => '75',
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
                            'popis' => 'Leták v igelitce!',
                            'ikona' => 'file alternate',
                        ],
                    ],
                    'imgVystavovateleAttributes' => [
                        'src' => $static_ref.'konplan'.$logo_ref,
                        'alt' => 'Firma Konplan',
                        'width' => '',
                        'height' => '100',
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
                        'src' => $static_ref.'mdelektronik'.$logo_ref,
                        'alt' => '',
                        'width' => '',
                        'height' => '',
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
                        'height' => '100',
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
                        'height' => '100',
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
                        'height' => '100',
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
                        'src' => $static_ref.'akka'.$logo_ref,
                        'alt' => '',
                        'width' => '',
                        'height' => '',
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
                        'height' => '100',
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
                        'height' => '100',
                    ]
                ],
            ]
        ]
    ]
?>

    <div class="online-stanky">
        <div class="ui stackable centered grid">
            <?= $this->repeat(__DIR__.'/hala/rozvrzeni-vystavovatelu.php', $vystavovatele) ?>
        </div>
    </div>

