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

############################
$shortName = 'akka';
############################

$monitorFilename = Configuration::files()['presenter'].$shortName.'/assets/monitor-stanek.jpg';
$monitorIsReadable = is_readable($monitorFilename);
$videoMp4Filename = Configuration::files()['presenter'].$shortName.'/movies/video-stanek-MP4.mp4';
$videoMp4IsReadable = is_readable($videoMp4Filename);
$videoWebmFilename = Configuration::files()['presenter'].$shortName.'/movies/video-stanek-WEBM.mp4';
$videoWebmIsReadable = is_readable($videoWebmFilename);
$stanek_ref = '/assets/stanek.png';


$letakAttributesClass = ['class' => 'letak-v-igelitce'];

$posters = [

    ];
$letak = [];
foreach ($posters as $poster) {
    $letak[] = [
        'letakAttributes' => $letakAttributesClass +
        [
            'src' => Configuration::files()['presenter']."poster/$poster.jpg",
            'alt' => "$poster",
        ],
        'downloadAttributes' => [
            'href' => Configuration::files()['presenter']."poster/$poster.pdf",
            'download' => "$poster",
        ]
    ];
};

$buttonTitle = [
    'Naše poradny'
];

foreach ($buttonTitle as $title) {
    $buttony[] = [
        'text' => $title,
        'odkaz' => 'javascript: document.getElementById(\''.Configuration::componentController()['prettyUrlCallable']($title).'\').scrollIntoView();',
    ];
};

$firma = [
    'nazev' => 'CzechInvest',
    'videoAttributes' => [
        'poster' => Configuration::files()['presenter'].$shortName.$monitor_ref,
    ],
    'videoSourceSrc' => [
        ['src' => Configuration::files()['presenter'].$shortName.$video_MP4_ref, 'type' => 'video/mp4'],
        ['src' => Configuration::files()['presenter'].$shortName.$video_WEBM_ref, 'type' => 'video/webm'],
    ],
    'imgStankuAttributes' => [
        'src' => Configuration::files()['presenter'].$shortName.$stanek_ref,
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
            'btnClass' => 'btn-in',
            'modalID' => 'modal_18',
            'iframe' => '<a href="https://www.linkedin.com/company/konplancz" target="_blank"><img src="'.Configuration::files()['presenter'].$shortName.'/assets/linkedin.png" alt="profil LinkednIn" height="" width="100%"/></a>',
            'odkazNaProfil' => 'https://www.linkedin.com/company/konplancz'
        ]
    ],
    'chat' => [
        'ikonaChatu' => 'chat circle',
        'text' => '<p>Chatovat s námi můžete přes Facebook Messenger</p>
                   <p>na adrese: <a href="http://m.me/KonplanCZ" target="_blank">http://m.me/KonplanCZ</a></p>',
        'odkaz' => '<a class="ui button" href="http://m.me/KonplanCZ" target="_blank">Přejít na Facebook Messenger</a>'
    ],
    'buttony' => $buttony,
    'letak' => $letak
];
