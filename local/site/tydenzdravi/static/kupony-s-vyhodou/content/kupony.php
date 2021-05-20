<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

    $radekKuponu = [
        [
            'kupon' => [
                [
                    'imgKuponuAttributes' => [
                        'class' => 'kupon-img',
                        'src' => '@download/kupon-ForActive.jpg',
                        'alt' => 'Kupón For Active',
                        'width' => '450',
                        'height' => '300',
                    ],
                    'odkazKeStazeniAttributes' => [
                        'href' => '@download/kupon-ForActive.jpg',
                        'download' => 'Kupón For Active'
                    ],
                    'text' => 'Stáhnout kupón'
                ],
                [
                    'imgKuponuAttributes' => [
                        'class' => 'kupon-img',
                        'src' => '@download/kupon-LekarnaVBezovce.jpg',
                        'alt' => 'Kupón Lékárna V Bezovce',
                        'width' => '450',
                        'height' => '300',
                    ],
                    'odkazKeStazeniAttributes' => [
                        'href' => '@download/kupon-LekarnaVBezovce.jpg',
                        'download' => 'Kupón Lékárna V Bezovce'
                    ],
                    'text' => 'Stáhnout kupón'
                ],
            ]
        ],
        [
            'kupon' => [
                [
                    'imgKuponuAttributes' => [
                        'class' => 'kupon-img',
                        'src' => '@download/kupon-OptikStudio.jpg',
                        'alt' => 'Kupón Optik Studio',
                        'width' => '450',
                        'height' => '300',
                    ],
                    'odkazKeStazeniAttributes' => [
                        'href' => '@download/kupon-OptikStudio.jpg',
                        'download' => 'Kupón Optik Studio'
                    ],
                    'text' => 'Stáhnout kupón'
                ],
                [
                    'imgKuponuAttributes' => [
                        'class' => 'kupon-img',
                        'src' => '@download/kupon-ULibusky.jpg',
                        'alt' => 'Kupón U Lidušky',
                        'width' => '450',
                        'height' => '300',
                    ],
                    'odkazKeStazeniAttributes' => [
                        'href' => '@download/kupon-ULibusky.jpg',
                        'download' => 'Kupón U Lidušky'
                    ],
                    'text' => 'Stáhnout kupón'
                ],
            ]
        ],
        [
            'kupon' => [
                [
                    'imgKuponuAttributes' => [
                        'class' => 'kupon-siroky-img',
                        'src' => '@download/kupon-Grafia.jpg',
                        'alt' => 'Kupón Grafia',
                        'width' => '590',
                        'height' => '250',
                    ],
                    'odkazKeStazeniAttributes' => [
                        'href' => '@download/kupon-Grafia.jpg',
                        'download' => 'Kupón Grafia'
                    ],
                    'text' => 'Stáhnout kupón'
                ]

            ]
        ]

    ]
?>

<div class="kupony">
    <div class="ui stackable centered grid">
        <?= $this->repeat(__DIR__.'/kupony/rozvrzeni-kuponu.php', $radekKuponu) ?>
    </div>
</div>

