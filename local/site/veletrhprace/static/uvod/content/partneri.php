<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

    $placeneLogoValeo = [
        'wwwPartnera' => 'https://www.valeo.com/cs/ceska-republika/',
        'imgPartneraAttributes' => [
            'src' => '@images/valeo_logo_web.png',
            'alt' => 'Logo Valeo',
            'width' => '',
            'height' => '130',
            'class' => 'logo-vysoke'
        ]
    ];
    $partneri = [
        [
            'radekPartneru' => [
                [
                    'wwwPartnera' => 'https://www.uradprace.cz/',
                    'imgPartneraAttributes' => [
                        'src' => '@images/urad_prace.png',
                        'alt' => 'Logo ÚP ČR',
                        'width' => '',
                        'height' => '100',
                        'class' => 'logo-ctverec'
                    ]
                ],
                [
                    'wwwPartnera' => 'https://www.zcu.cz/cs/index.html',
                    'imgPartneraAttributes' => [
                        'src' => '@images/zcu.png',
                        'alt' => 'Logo Západočeská univerzita',
                        'width' => '',
                        'height' => '100',
                        'class' => 'logo-ctverec'
                    ]
                ],
                [
                    'wwwPartnera' => 'https://www.komora.cz/',
                    'imgPartneraAttributes' => [
                        'src' => '@images/komora_logo_web.png',
                        'alt' => 'Logo Hospodářská komora',
                        'width' => '',
                        'height' => '100',
                        'class' => 'logo-ctverec'
                    ]
                ],

            ]
        ],
        [
            'radekPartneru' => [
                [
                    'wwwPartnera' => 'https://www.aivd.cz/',
                    'imgPartneraAttributes' => [
                        'src' => '@images/aivd.png',
                        'alt' => 'Logo AIVD',
                        'width' => '',
                        'height' => '100',
                        'class' => 'logo-ctverec'
                    ]
                ],
                [
                    'wwwPartnera' => 'https://www.pzpk.cz/',
                    'imgPartneraAttributes' => [
                        'src' => '@images/pakt_namestnanosti.png',
                        'alt' => 'Logo Pakt zaměstnanosti',
                        'width' => '',
                        'height' => '85',
                        'class' => 'logo-obdelnik'
                    ],
                ],
                [
                    'wwwPartnera' => 'https://umo3.plzen.eu/',
                    'imgPartneraAttributes' => [
                        'src' => '@images/logo_umo3.png',
                        'alt' => 'Logo UMO3 Plzeň',
                        'width' => '',
                        'height' => '110',
                        'class' => 'logo-ctverec'
                    ],
                ],
            ]
        ],
        [
            'radekPartneru' => [
                [
                    'wwwPartnera' => 'https://www.ledovec.cz/',
                    'imgPartneraAttributes' => [
                        'src' => '@images/ledovec.png',
                        'alt' => 'Logo Ledovec',
                        'width' => '',
                        'height' => '130',
                        'class' => 'logo-vysoke'
                    ],
                ],
                [
                    'wwwPartnera' => 'https://plzensky.denik.cz/',
                    'imgPartneraAttributes' => [
                        'src' => '@images/plzen_denik.png',
                        'alt' => 'Logo Plzeňský deník',
                        'width' => '',
                        'height' => '85',
                        'class' => 'logo-obdelnik'
                    ],
                ],
                [
                    'wwwPartnera' => 'https://plzen.rozhlas.cz/',
                    'imgPartneraAttributes' => [
                        'src' => '@images/CRo_Plzen.png',
                        'alt' => 'Logo Český rozhlas Plzeň',
                        'width' => '',
                        'height' => '75',
                        'class' => 'logo-obdelnik'
                    ],
                ],
            ],
        ],
        [
            'radekPartneru' => [
                [
                    'wwwPartnera' => 'http://www.krasovska.cz/',
                    'imgPartneraAttributes' => [
                        'src' => '@images/krasovska-logo.png',
                        'alt' => 'Logo Krašovská',
                        'width' => '',
                        'height' => '110',
                        'class' => 'logo-ctverec'
                    ],
                ],
                [
                    'wwwPartnera' => 'https://www.czechinvest.org/cz',
                    'imgPartneraAttributes' => [
                        'src' => '@images/czechinvest-logo.png',
                        'alt' => 'Logo CzechInvest',
                        'width' => '',
                        'height' => '75',
                        'class' => 'logo-obdelnik'
                    ],
                ],
                [
                    'wwwPartnera' => 'https://www.cesnet.cz/',
                    'imgPartneraAttributes' => [
                        'src' => '@images/cesnet-logo.png',
                        'alt' => 'Logo Cesnet',
                        'width' => '',
                        'height' => '65',
                        'class' => 'logo-siroke'
                    ],
                ]
            ],
        ],
        [
            'radekPartneru' => [
                [
                    'wwwPartnera' => 'http://www.suip.cz/',
                    'imgPartneraAttributes' => [
                        'src' => '@images/suip.png',
                        'alt' => 'Logo SUIP',
                        'width' => '',
                        'height' => '55',
                        'class' => 'logo-siroke'
                    ],
                ],
                [
                    'wwwPartnera' => 'https://www.diakoniezapad.cz/',
                    'imgPartneraAttributes' => [
                        'src' => '@images/diakonie-logo.png',
                        'alt' => 'Logo Diakonie',
                        'width' => '',
                        'height' => '70',
                        'class' => 'logo-siroke'
                    ],
                ],
            ],
        ]
    ]
?>

<div class="blok-nadpis-loga">
    <p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll">Partneři</p>
    <div class="partneri-pozadi">
        <div class="ui stackable centered grid">
            <?= $this->insert(__DIR__.'/partneri/placeneLogo.php', $placeneLogoValeo) ?>
            <?= $this->repeat(__DIR__.'/partneri/rozvrzeni-partneru.php', $partneri) ?>
        </div>
    </div>
</div>
