<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

    $placeneLogoValeo = [
        'wwwPartnera' => 'https://www.valeo.com/cs/ceska-republika/',
        'imgPartneraAttributes' => [
            'src' => '@siteimages/valeo_logo_web.png',
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
                        'src' => '@siteimages/urad_prace.png',
                        'alt' => 'Logo ÚP ČR',
                        'width' => '',
                        'height' => '100',
                        'class' => 'logo-ctverec'
                    ]
                ],
                [
                    'wwwPartnera' => 'https://www.zcu.cz/cs/index.html',
                    'imgPartneraAttributes' => [
                        'src' => '@siteimages/zcu.png',
                        'alt' => 'Logo Západočeská univerzita',
                        'width' => '',
                        'height' => '100',
                        'class' => 'logo-ctverec'
                    ]
                ],
                [
                    'wwwPartnera' => 'https://www.komora.cz/',
                    'imgPartneraAttributes' => [
                        'src' => '@siteimages/komora_logo_web.png',
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
                        'src' => '@siteimages/aivd.png',
                        'alt' => 'Logo AIVD',
                        'width' => '',
                        'height' => '100',
                        'class' => 'logo-ctverec'
                    ]
                ],
                [
                    'wwwPartnera' => 'https://www.pzpk.cz/',
                    'imgPartneraAttributes' => [
                        'src' => '@siteimages/pakt_namestnanosti.png',
                        'alt' => 'Logo Pakt zaměstnanosti',
                        'width' => '',
                        'height' => '85',
                        'class' => 'logo-obdelnik'
                    ],
                ],
                [
                    'wwwPartnera' => 'https://umo3.plzen.eu/',
                    'imgPartneraAttributes' => [
                        'src' => '@siteimages/logo_umo3.png',
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
                        'src' => '@siteimages/ledovec.png',
                        'alt' => 'Logo Ledovec',
                        'width' => '',
                        'height' => '130',
                        'class' => 'logo-vysoke'
                    ],
                ],
                [
                    'wwwPartnera' => 'https://plzensky.denik.cz/',
                    'imgPartneraAttributes' => [
                        'src' => '@siteimages/plzen_denik.png',
                        'alt' => 'Logo Plzeňský deník',
                        'width' => '',
                        'height' => '85',
                        'class' => 'logo-obdelnik'
                    ],
                ],
                [
                    'wwwPartnera' => 'https://plzen.rozhlas.cz/',
                    'imgPartneraAttributes' => [
                        'src' => '@siteimages/CRo_Plzen.png',
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
                        'src' => '@siteimages/krasovska-logo.png',
                        'alt' => 'Logo Krašovská',
                        'width' => '',
                        'height' => '110',
                        'class' => 'logo-ctverec'
                    ],
                ],
                [
                    'wwwPartnera' => 'https://www.czechinvest.org/cz',
                    'imgPartneraAttributes' => [
                        'src' => '@siteimages/czechinvest-logo.png',
                        'alt' => 'Logo CzechInvest',
                        'width' => '',
                        'height' => '75',
                        'class' => 'logo-obdelnik'
                    ],
                ],
                [
                    'wwwPartnera' => 'https://www.cesnet.cz/',
                    'imgPartneraAttributes' => [
                        'src' => '@siteimages/cesnet-logo.png',
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
                        'src' => '@siteimages/suip.png',
                        'alt' => 'Logo SUIP',
                        'width' => '',
                        'height' => '55',
                        'class' => 'logo-siroke'
                    ],
                ],
                [
                    'wwwPartnera' => 'https://umo2.plzen.eu/',
                    'imgPartneraAttributes' => [
                        'src' => '@siteimages/logo_umo2.png',
                        'alt' => 'Logo UMO2 Plzeň',
                        'width' => '',
                        'height' => '120',
                        'class' => 'logo-ctverec'
                    ],
                ],
                [
                    'wwwPartnera' => 'https://www.diakoniezapad.cz/',
                    'imgPartneraAttributes' => [
                        'src' => '@siteimages/diakonie-logo.png',
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
