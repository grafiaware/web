<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Pes\Text\Text;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */

    $logoKeStazeni = [
        [
            'logo' => [
                'ukazkaLoga' => [
                    'imgVariantaLogaAttributes' =>  [
                        'src' => '@download/LogoTZ.jpg',
                        'alt' => 'Logo Týden zdraví',
                        'width' => '362',
                        'height' => '130'
                    ],
                ],
                'stazeniLoga' => [
                    [
                        'odkazKeStazeniAttributes' => [
                            'href' => '@download/LOGO_Týden_zdraví.ai',
                            'download' => 'LOGA Týden zdraví.ai'
                        ],
                        'text' => 'Logo ve formátu AI'
                    ],
                    [
                        'odkazKeStazeniAttributes' => [
                            'href' => '@download/LOGO_Týden_zdraví_2.png',
                            'download' => 'LOGO Týden zdraví horizontálně.png'
                        ],
                        'text' => 'Logo ve formátu PNG'
                    ],
                    [
                        'odkazKeStazeniAttributes' => [
                            'href' => '@download/LOGO_Týden_zdraví_JPG_1.jpg',
                            'download' => 'LOGO Týden zdraví horizontálně.jpg'
                        ],
                        'text' => 'Logo ve formátu JPG'
                    ]
                ],

            ]
        ],
        [
            'logo' => [
                'ukazkaLoga' => [
                    'imgVariantaLogaAttributes' =>  [
                        'src' => '@download/LogoTZ_var2.jpg',
                        'alt' => 'Logo Týden zdraví',
                        'width' => '148',
                        'height' => '200'
                    ],
                ],
                'stazeniLoga' => [
                    [
                        'odkazKeStazeniAttributes' => [
                            'href' => '@download/LOGO_Týden_zdraví.ai',
                            'download' => 'LOGA Týden zdraví.ai'
                        ],
                        'text' => 'Logo ve formátu AI'
                    ],
                    [
                        'odkazKeStazeniAttributes' => [
                            'href' => '@download/LOGO_Týden_zdraví_1.png',
                            'download' => 'LOGO Týden zdraví vertikálně.png'
                        ],
                        'text' => 'Logo ve formátu PNG'
                    ],
                    [
                        'odkazKeStazeniAttributes' => [
                            'href' => '@download/LOGO_Týden_zdraví_JPG_2.jpg',
                            'download' => 'LOGO Týden zdraví vertikálně.jpg'
                        ],
                        'text' => 'Logo ve formátu JPG'
                    ]
                ],

            ]
        ]
    ];
?>

<div class="logo-ke-stazeni">
    <div class="ui centered grid">
        <?= $this->repeat(__DIR__.'/logo/rozvrzeni-variant.php', $logoKeStazeni) ?>
    </div>
</div>
