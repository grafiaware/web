<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

    $logaSTextem = [
        [
            'odkazBodyAttributes' => [
                'class' => 'link-img',
                'href' => 'https://www.foractiv-plzen.cz/',
                'target' => '_blank'
            ],
            'imagesBodyAttributes' => [
                'class' => 'logo-siroke',
                'src' => 'images/logo-FA.jpg',
                'alt' => 'Logo For activ',
                'width' => '335',
                'height' => '105'
            ],
            'textFirmy' => 'Jsme firma zabývající se prodejem zdravé a sportovní výživy s maximální funkčností , ale také vysokou kvalitou. Produktům dobře rozumíme a umíme vybrat takové, které se nejlépe hodí pro daný účel. V oblasti zdraví jsme hrdým dovozcem prémiových značek Viridian a EkoLife.'
        ]
    ];
            
    $logaSProklikem = [
        [
            'radekLog' => [
                [
                    'odkazBodyAttributes' => [
                        'class' => 'link-img',
                        'href' => 'https://lekarnavbezovce.cz/',
                        'target' => '_blank'
                    ],
                    'imagesBodyAttributes' => [
                        'class' => 'logo-siroke',
                        'src' => 'images/logo-lekarna-bezovka.png',
                        'alt' => 'Logo Lékárna V Bezovce',
                        'width' => '335',
                        'height' => '80'
                    ]
                ],
                [
                    'odkazBodyAttributes' => [
                        'class' => 'link-img',
                        'href' => 'https://www.obchodulidusky.cz/',
                        'target' => '_blank'
                    ],
                    'imagesBodyAttributes' => [
                        'class' => 'logo-ctverec',
                        'src' => 'images/logo_U_Lidusky.jpg',
                        'alt' => 'Logo U Lidušky',
                        'width' => '185',
                        'height' => '200'
                    ]
                ],
                [
                    'odkazBodyAttributes' => [
                        'class' => 'link-img',
                        'href' => 'https://www.ledovec.cz/',
                        'target' => '_blank'
                    ],
                    'imagesBodyAttributes' => [
                        'class' => 'logo-vysoke',
                        'src' => 'images/logo-Ledovec.png',
                        'alt' => 'Logo Ledovec',
                        'width' => '120',
                        'height' => '200'
                    ]
                ]
            ]
        ],
        [
            'radekLog' => [
                [
                    'odkazBodyAttributes' => [
                        'class' => 'link-img',
                        'href' => 'https://www.zachrankaapp.cz/',
                        'target' => '_blank'
                    ],
                    'imagesBodyAttributes' => [
                        'class' => 'logo-siroke',
                        'src' => 'images/logo-zachranka.png',
                        'alt' => 'Logo Záchranka',
                        'width' => '335',
                        'height' => ''
                    ]
                ],
                [
                    'odkazBodyAttributes' => [
                        'class' => 'link-img',
                        'href' => 'http://www.grafia.cz/',
                        'target' => '_blank'
                    ],
                    'imagesBodyAttributes' => [
                        'class' => 'logo-ctverec',
                        'src' => 'images/LogoGrafia.jpg',
                        'alt' => 'Logo Grafia',
                        'width' => '230',
                        'height' => '159'
                    ]
                ],
                [
                    'odkazBodyAttributes' => [
                        'class' => 'link-img',
                        'href' => 'https://www.sadynebilovy.cz/',
                        'target' => '_blank'
                    ],
                    'imagesBodyAttributes' => [
                        'class' => 'logo-ctverec',
                        'src' => 'images/logo-sady.jpg',
                        'alt' => 'Logo Záchranka',
                        'width' => '185',
                        'height' => '192'
                    ]
                ],
            ],
        ],
        [
            'radekLog' => [
                [
                    'odkazBodyAttributes' => [
                        'class' => 'link-img',
                        'href' => 'https://www.optiksvarc.cz/',
                        'target' => '_blank'
                    ],
                    'imagesBodyAttributes' => [
                        'class' => 'logo-siroke',
                        'src' => 'images/logo-optiksvarc.jpg',
                        'alt' => 'Logo Optik Švarc',
                        'width' => '335',
                        'height' => '148'
                    ]
                ],
                [
                    'odkazBodyAttributes' => [
                        'class' => 'link-img',
                        'href' => 'http://www.krasovska.cz/',
                        'target' => '_blank'
                    ],
                    'imagesBodyAttributes' => [
                        'class' => 'logo-siroke',
                        'src' => 'images/logo_Krasovska.jpg',
                        'alt' => 'Logo Krašovská',
                        'width' => '335',
                        'height' => '115'
                    ]
                ],
                [
                    'odkazBodyAttributes' => [
                        'class' => 'link-img',
                        'href' => 'http://www.studiofitnesska.cz/',
                        'target' => '_blank'
                    ],
                    'imagesBodyAttributes' => [
                        'class' => 'logo-siroke',
                        'src' => 'images/logo-fitnesska.png',
                        'alt' => 'Logo FITNESSKA',
                        'width' => '335',
                        'height' => '68'
                    ]
                ]
            ]
        ],
    ];
    
    $odkazyBezLog = [
        [
            'radekOdkazu' => [
                [
                    'odkazBodyAttributes' => [
                        'class' => 'ui massive secondary basic fluid button',
                        'href' => 'https://www.mvcr.cz/clanek/telefonni-psychologicke-linky-pro-seniory-deti-a-dospele.aspx',
                        'target' => '_blank'
                    ],
                    'odkazText' => 'Krizové linky — kam zavolat? A online pomoc v ČR'
                ],
                [
                    'odkazBodyAttributes' => [
                        'class' => 'ui massive secondary basic fluid button',
                        'href' => 'http://otuzilci-plzen.cz/',
                        'target' => '_blank'
                    ],
                    'odkazText' => 'Klub sportovních otužilců Plzeň'
                ],
                [
                    'odkazBodyAttributes' => [
                        'class' => 'ui massive secondary basic fluid button',
                        'href' => 'https://koronavirus.plzen.eu/',
                        'target' => '_blank'
                    ],
                    'odkazText' => 'Co dělat v různých životních situacích'
                ]
            ]
        ],
        [
            'radekOdkazu' => [
                [
                    'odkazBodyAttributes' => [
                        'class' => 'ui massive secondary basic fluid button',
                        'href' => 'https://www.totemplzen.cz/bolevec/projekt-mosty/',
                        'target' => '_blank'
                    ],
                    'odkazText' => 'TOTEM — PROJEKT MOSTY'
                ],
                [
                    'odkazBodyAttributes' => [
                        'class' => 'ui massive secondary basic fluid button',
                        'href' => 'https://www.facebook.com/Farnost-u-katedr%C3%A1ly-v-Plzni-109920727303535',
                        'target' => '_blank'
                    ],
                    'odkazText' => 'Duchovní útěcha'
                ],
                [
                    'odkazBodyAttributes' => [
                        'class' => 'ui massive secondary basic fluid button',
                        'href' => 'http://www.socialniprace.cz/index.php?sekce=62&podsekce=86',
                        'target' => '_blank'
                    ],
                    'odkazText' => 'Koronavirus v sociální práci'
                ]
            ]
        ],
        [
            'radekOdkazu' => [
                [
                    'odkazBodyAttributes' => [
                        'class' => 'ui massive secondary basic fluid button',
                        'href' => 'https://iure.org/d/14/program-pravni-pomoc',
                        'target' => '_blank'
                    ],
                    'odkazText' => 'Bezplatná právní pomoc lidem nad 50 let'
                ],
                [
                    'odkazBodyAttributes' => [
                        'class' => 'ui massive secondary basic fluid button',
                        'href' => 'https://www.justice.cz/web/msp?clanek=jak-za-soucasne-situace-resit-exekuce-a-insolvence-&fbclid=IwAR2-W4s98U-Cy6M636Hq3l26ZjvcSbeut4msEIxQ4szbVCtuBd9ETkbJbGo',
                        'target' => '_blank'
                    ],
                    'odkazText' => 'Portál Justice — jak teď řešit exekuce a insolvence'
                ],
                [
                    'odkazBodyAttributes' => [
                        'class' => 'ui massive secondary basic fluid button',
                        'href' => 'http://www.uracr.cz/koroporadna?fbclid=IwAR3WBuEnUCoVGVbCJSWyqQqg9Fy-j47_3QftcGRc-RQesg-ysfSrNFDr8DQ',
                        'target' => '_blank'
                    ],
                    'odkazText' => 'Unie rodinných poradců — Korona poradna'
                ]
            ]
        ],
        [
            'radekOdkazu' => [
                [
                    'odkazBodyAttributes' => [
                        'class' => 'ui massive secondary basic fluid button',
                        'href' => 'http://centrumlocika.cz/odbornici/prakticky-pruvodce-spolupraci-pro-pracovniky-ospod',
                        'target' => '_blank'
                    ],
                    'odkazText' => 'Centrum Locika — pomoc pro děti ohrožené domácím násilím'
                ],
                [
                    'odkazBodyAttributes' => [
                        'class' => '',
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazText' => ''
                ],
                [
                    'odkazBodyAttributes' => [
                        'class' => '',
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazText' => ''
                ]
            ]
        ]
        
    ];
?>

<div class="body-zdravi">
    <div class="ui stackable centered grid">
       <?= $this->repeat(__DIR__.'/bodyZdravi/logoSTextem.php', $logaSTextem) ?>
       <?= $this->repeat(__DIR__.'/bodyZdravi/rozvrzeniLogSproklikem.php', $logaSProklikem) ?> 
       <?= $this->repeat(__DIR__.'/bodyZdravi/rozvrzeniOdkazuBezLog.php', $odkazyBezLog) ?> 
    </div>
</div>