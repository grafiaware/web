<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

    $parneri = [
        [
            'radekPartneru' => [
                [
                    'wwwPartnera' => 'https://www.uradprace.cz/',
                    'imgPartneraAttributes' => [
                        'src' => 'images/urad_prace.png',
                        'alt' => 'Logo ÚP ČR',
                        'width' => '',
                        'height' => '100',
                    ]
                ],
                [
                    'wwwPartnera' => 'https://www.zcu.cz/cs/index.html',
                    'imgPartneraAttributes' => [
                        'src' => 'images/zcu.png',
                        'alt' => 'Logo Západočeská univerzita',
                        'width' => '',
                        'height' => '100',
                    ]
                ],
                
                [
                    'wwwPartnera' => 'http://www.suip.cz/',
                    'imgPartneraAttributes' => [
                        'src' => 'images/suip.png',
                        'alt' => 'Logo Státní úřad inspekce práce',
                        'width' => '',
                        'height' => '60',
                    ]
                ],
                
            ]
        ],
        [
            'radekPartneru' => [
                [
                    'wwwPartnera' => 'https://www.pzpk.cz/',
                    'imgPartneraAttributes' => [
                        'src' => 'images/pakt_namestnanosti.png',
                        'alt' => 'Logo Pakt zaměstnanosti',
                        'width' => '',
                        'height' => '85',
                    ],
                ],
                [
                    'wwwPartnera' => 'https://www.aivd.cz/',
                    'imgPartneraAttributes' => [
                        'src' => 'images/aivd.png',
                        'alt' => 'Logo AIVD',
                        'width' => '',
                        'height' => '100',
                    ]
                ],
                [
                    'wwwPartnera' => 'http://www.grafia.cz/',
                    'imgPartneraAttributes' => [
                        'src' => 'images/logo_grafia.png',
                        'alt' => 'Logo Grafia',
                        'width' => '',
                        'height' => '110',
                    ],
                ],
                
            ]
        ],
        [
            'radekPartneru' => [
                [
                    'wwwPartnera' => 'https://www.ledovec.cz/',
                    'imgPartneraAttributes' => [
                        'src' => 'images/ledovec.png',
                        'alt' => 'Logo Ledovec',
                        'width' => '',
                        'height' => '130',
                    ],
                ],
                [
                    'wwwPartnera' => 'https://www.komora.cz/',
                    'imgPartneraAttributes' => [
                        'src' => 'images/hk_cr.png',
                        'alt' => 'Logo Hospodářská komora',
                        'width' => '',
                        'height' => '75',
                    ]
                ],
                [
                    'wwwPartnera' => 'https://www.dzklatovy.cz/',
                    'imgPartneraAttributes' => [
                        'src' => 'images/dzk.png',
                        'alt' => 'Logo Drůbežářský závod Klatovy',
                        'width' => '',
                        'height' => '130',
                    ],
                ],
            ]
        ],
        [
            'radekPartneru' => [
                [
                    'wwwPartnera' => 'https://plzen.eurocentra.cz/',
                    'imgPartneraAttributes' => [
                        'src' => 'images/eurocentrum.png',
                        'alt' => 'Logo Eurocentrum',
                        'width' => '',
                        'height' => '60',
                    ],
                ],
                [
                    'wwwPartnera' => 'https://plzensky.denik.cz/',
                    'imgPartneraAttributes' => [
                        'src' => 'images/plzen_denik.png',
                        'alt' => 'Logo Plzeňský deník',
                        'width' => '',
                        'height' => '85',
                    ],
                ]
            ]
        ]
    ]
?>

<div class="blok-nadpis-loga">
    <p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll">Partneři</p>
    <div class="partneri-pozadi">
        <div class="ui stackable centered grid">
            <?= $this->repeat(__DIR__.'/partneri/rozvrzeni-partneru.php', $parneri) ?>
        </div>
    </div>
</div>
