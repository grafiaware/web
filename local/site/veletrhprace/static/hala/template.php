<?php

use Site\Configuration;

use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

$static_ref = Configuration::files()['presenter'];
$logo_ref = '/assets/logo.png';

$headline = 'Online stánky';
$perex = 'Poznejte svého budoucího zaměstnavatele na našich online stáncích.';

$monitorFilename = Configuration::files()['@images'].'monitor-stanek.jpg';
$monitorIsReadable = is_readable($monitorFilename);
$videoMp4Filename = Configuration::files()['@movies'].'smycka-MP4.mp4';
$videoMp4IsReadable = is_readable($videoMp4Filename);
$videoWebmFilename = Configuration::files()['@movies'].'smycka-WEBM.webm';
$videoWebmIsReadable = is_readable($videoWebmFilename);

$promoVideo = [
    'videoAttributes' => [
        'poster' => $monitorIsReadable ? $monitorFilename : "",
    ],
    'videoSourceSrc' => [
        $videoMp4IsReadable ? ['src' => $videoMp4Filename, 'type' => 'video/mp4'] : null,
        $videoWebmIsReadable ? ['src' => $videoWebmFilename, 'type' => 'video/webm'] : null,
    ]
];

$infoForRegistered = [
        [
            'description' => 'Tuto firmu jste si již prohlédli',
            'icon' => 'history',
        ],
        [
            'description' => 'Individuální pohovor <br/> <b>ve čtvrtek 18. 3. v 10:00</b>',
            'icon' => 'calendar alternate',
        ],
        [
            'description' => 'Leták v <a>igelitce!</a>',
            'icon' => 'file alternate',
        ],
];

$exhibitor = [
        [
            'row' => [
                [
                    'name' => 'Grafia',
                    'urlStand' => 'web/v1/page/block/grafia',
                    'logoAttributes' => [
                        'src' => $static_ref.'grafia'.$logo_ref,
                        'alt' => 'Firma Grafia',
                    ]
                ],
                [
                    'name' => 'Daikin',
                    'urlStand' => 'web/v1/page/block/daikin',
                    'logoAttributes' => [
                        'src' => $static_ref.'daikin'.$logo_ref,
                        'alt' => 'Firma Daikin',
                    ]
                ],
            ]
        ],
        [
            'row' => [
                [
                    'name' => 'Konplan',
                    'urlStand' => 'web/v1/page/block/konplan',
                    'logoAttributes' => [
                        'src' => $static_ref.'konplan'.$logo_ref,
                        'alt' => 'Firma Konplan',
                    ]
                ],
                [
                    'name' => 'MD Elektronik',
                    'urlStand' => 'web/v1/page/block/mdelektronik',
                    'logoAttributes' => [
                        'src' => $static_ref.'mdelektronik'.$logo_ref,
                        'alt' => 'Firma MD Elektronik',
                    ]
                ],
            ]
        ],
        [
            'row' => [
                [
                    'name' => 'Úřad práce',
                    'urlStand' => 'web/v1/page/block/up',
                    'logoAttributes' => [
                        'src' => $static_ref.'up'.$logo_ref,
                        'alt' => 'Firma ÚP',
                    ]
                ],
                [
                    'name' => 'Stoelzle',
                    'urlStand' => 'web/v1/page/block/stoelzle',
                    'logoAttributes' => [
                        'src' => $static_ref.'stoelzle'.$logo_ref,
                        'alt' => 'Firma Stoelzle',
                    ]
                ],
            ]
        ],
        [
            'row' => [
                [
                    'name' => 'Wienerberger',
                    'urlStand' => 'web/v1/page/block/wienerberger',
                    'logoAttributes' => [
                        'src' => $static_ref.'wienerberger'.$logo_ref,
                        'alt' => 'Firma Wienerberger',
                    ]
                ],
                [
                    'name' => 'Akka',
                    'urlStand' => 'web/v1/page/block/akka',
                    'logoAttributes' => [
                        'src' => $static_ref.'akka'.$logo_ref,
                        'alt' => 'Firma Akka',
                    ]
                ],
            ]
        ],
        [
            'row' => [
                [
                    'name' => 'Kermi',
                    'urlStand' => 'web/v1/page/block/kermi',
                    'logoAttributes' => [
                        'src' => $static_ref.'kermi'.$logo_ref,
                        'alt' => 'Firma Kermi',
                    ]
                ],
                [
                    'name' => 'Drůběžářský závod Klatovy',
                    'urlStand' => 'web/v1/page/block/dzk',
                    'logoAttributes' => [
                        'src' => $static_ref.'dzk'.$logo_ref,
                        'alt' => 'Firma DZK',
                    ]
                ],
            ]
        ],
        [
            'row' => [
                [
                    'name' => 'Possehl',
                    'urlStand' => 'web/v1/page/block/possehl',
                    'logoAttributes' => [
                        'src' => $static_ref.'possehl'.$logo_ref,
                        'alt' => 'Firma Possehl',
                    ]
                ],
                [
                    'name' => 'Valeo',
                    'urlStand' => 'web/v1/page/block/valeo',
                    'logoAttributes' => [
                        'src' => $static_ref.'valeo'.$logo_ref,
                        'alt' => 'Firma Valeo',
                    ]
                ],
            ]
        ]
    ];
//            echo    "<pre>".var_dump($exhibitor)."</pre>";
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