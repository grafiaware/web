<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

    $radekParneru = [
        [
            'partner' => [
                [
                    'wwwPartnera' => 'https://umo1.plzen.eu/',
                    'imgPartneraAttributes' => [
                        'src' => 'images/logo_umo1_web.jpg',
                        'alt' => 'Logo UMO1 Plzeň',
                        'width' => '348',
                        'height' => '84',
                    ]
                ],
                [
                    'wwwPartnera' => 'https://umo3.plzen.eu/',
                    'imgPartneraAttributes' => [
                        'src' => 'images/logo_umo3_web.jpg',
                        'alt' => 'Logo UMO3 Plzeň',
                        'width' => '200',
                        'height' => '200',
                    ]
                ],
                [
                    'wwwPartnera' => 'https://www.fnplzen.cz/',
                    'imgPartneraAttributes' => [
                        'src' => 'images/logo_FN_web.jpg',
                        'alt' => 'Logo FN Plzeň',
                        'width' => '224',
                        'height' => '120',
                    ]
                ]
            ]
        ],
        [
            'partner' => [
                [
                    'wwwPartnera' => 'https://plzensky.denik.cz/',
                    'imgPartneraAttributes' => [
                        'src' => 'images/logo-plzenskydenik.jpg',
                        'alt' => 'Logo Deník',
                        'width' => '276',
                        'height' => '85',
                    ],
                ],
                [
                    'wwwPartnera' => 'https://czv.zcu.cz/',
                    'imgPartneraAttributes' => [
                        'src' => 'images/logo-univerzita.jpg',
                        'alt' => 'Logo CZV ZČU',
                        'width' => '348',
                        'height' => '129',
                    ],
                ],
                [
                    'wwwPartnera' => 'https://plzen.rozhlas.cz/',
                    'imgPartneraAttributes' => [
                        'src' => 'images/logo-CRo.jpg',
                        'alt' => 'Logo Rozhlas',
                        'width' => '227',
                        'height' => '110',
                    ]
                ]
            ]
        ]
    ]
?>

<div class="blok-nadpis-loga">
    <div class="ui stackable centered grid">
        <div class="sixteen wide column">
            <div class="primarni-barva podklad nadpis vlevo">
                <p>Partneři</p>
            </div>
            <div class="velky text vlevo okraje">
                <p>Děkujeme za podporu partnerům:</p>
            </div>
        </div>
        <?= $this->repeat(__DIR__.'/partneri/rozvrzeniPartneru.php', $radekParneru) ?>
    </div>
</div>
