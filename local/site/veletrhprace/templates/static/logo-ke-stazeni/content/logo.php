<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

    $logoKeStazeni = [
        [
            'logo' => [
                'ukazkaLoga' => [
                    'imgVariantaLogaAttributes' =>  [
                        'src' => 'images/LogoTZ.jpg',
                        'alt' => 'Logo Týden zdraví',
                        'width' => '362',
                        'height' => '130'
                    ],
                ],
                'stazeniLoga' => [
                    [
                        'odkazKeStazeniAttributes' => [
                            'href' => 'files/LOGO_Týden_zdraví.ai',
                            'download' => 'LOGA Týden zdraví.ai'
                        ],
                        'text' => 'Logo ve formátu AI'
                    ],
                    [
                        'odkazKeStazeniAttributes' => [
                            'href' => 'files/LOGO_Týden_zdraví_2.png',
                            'download' => 'LOGO Týden zdraví horizontálně.png'
                        ],
                        'text' => 'Logo ve formátu PNG'
                    ],
                    [
                        'odkazKeStazeniAttributes' => [
                            'href' => 'files/LOGO_Týden_zdraví_JPG_1.jpg',
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
                        'src' => 'images/LogoTZ_var2.jpg',
                        'alt' => 'Logo Týden zdraví',
                        'width' => '148',
                        'height' => '200'
                    ],
                ],
                'stazeniLoga' => [
                    [
                        'odkazKeStazeniAttributes' => [
                            'href' => 'files/LOGO_Týden_zdraví.ai',
                            'download' => 'LOGA Týden zdraví.ai'
                        ],
                        'text' => 'Logo ve formátu AI'
                    ],
                    [
                        'odkazKeStazeniAttributes' => [
                            'href' => 'files/LOGO_Týden_zdraví_1.png',
                            'download' => 'LOGO Týden zdraví vertikálně.png'
                        ],
                        'text' => 'Logo ve formátu PNG'
                    ],
                    [
                        'odkazKeStazeniAttributes' => [
                            'href' => 'files/LOGO_Týden_zdraví_JPG_2.jpg',
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
