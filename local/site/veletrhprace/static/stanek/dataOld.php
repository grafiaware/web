<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Site\Configuration;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

$modalAtributy = [
    "id" => "modal_12",
    "class"=> ["ui tiny longer", "modal"]
];

$static_ref = '_www_vp_files/static/';
$monitor_ref = '/assets/monitor-stanek.jpg';
$video_ref = '/movie/video-stanek.mp4';
$stanek_ref = '/assets/stanek.png';

$letakJPG_ref = '/poster/letak.png';
$letakPDF_ref = '/poster/letak.pdf';

$shortName = 'konplan';
$letakAttributesClass = ['class' => 'letak-v-igelitce'];

$firma = [
    'nazev' => 'Konplan s.r.o.',
    'videoAttributes' => [
        'poster' => $static_ref.$shortName.$monitor_ref,
//        'poster' => $static_ref.'konplan/assets/monitor-na-stanek1.jpg',   // nemám obrázek - opravit
    ],
    'videoSourceSrc' => $static_ref.$shortName.$video_ref,
    'imgStankuAttributes' => [
        'src' => $static_ref.$shortName.$stanek_ref,
        'alt' => 'stánek firmy',
    ],
    'socialniSiteIframe' => [
        [
            'ikonaSocialniSite' => 'facebook circle',
            'nazevSocialniSite' => 'facebook',
            'btnClass' => 'btn-fb',
            'modalID' => 'modal_15',
            'iframe' => '<div class="fb-page" data-href="https://www.facebook.com/KonplanCZ/" data-tabs="timeline" data-width="" data-height="500" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/veletrhprace/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/veletrhprace/">Veletrh práce a vzdělávání Plzeň - Klíč k příležitostem</a></blockquote></div>',
            'odkazNaProfil' => 'https://www.facebook.com/KonplanCZ/'
        ],
        [
            'ikonaSocialniSite' => 'linkedin circle',
            'nazevSocialniSite' => 'LinkedIn',
            'btnClass' => 'btn-ig',
            'modalID' => 'modal_16',
            'iframe' => '<a href="https://www.linkedin.com/company/konplancz" target="_blank"><img src="'.$static_ref.'konplan/assets/linkedin.png" alt="profil LinkednIn Konplan" height="" width="100%"/></a>',
            'odkazNaProfil' => 'https://www.linkedin.com/company/konplancz'
        ]
    ],
    'chat' => [
        'ikonaChatu' => 'chat circle',
    ],
    'buttony' => [
        [
            'text' => 'Pracovní pozice',
            'odkaz' => ''
        ],
        [
            'text' => 'Náš program',
            'odkaz' => ''
        ],
        [
            'text' => 'Chci na online pohovor',
            'odkaz' => ''
        ],
        [
            'text' => 'Chci navázat kontakt',
            'odkaz' => ''
        ],
    ],
    'letak' => [
        [
            'nazev' => '',
            'letakAttributes' => $letakAttributesClass +
            [
                'src' => 'images/letak-na-prednasku.jpg',
                'alt' => 'leták1',
            ],
            'downloadAttributes' => [
                'href' => 'download/letak-na-prednasku.pdf',
                'download' => 'leták 1',
            ]
        ],
        [
            'letakAttributes' => $letakAttributesClass +
            [
                'src' => 'images/moje-budoucnost-letakA5.jpg',
                'alt' => 'leták2',
            ],
            'downloadAttributes' => [
                'href' => 'download/moje-budoucnost-letakA5.pdf',
                'download' => 'leták 2',
            ]
        ]
    ],
];
