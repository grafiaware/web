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

$monitor_ref = '/assets/monitor-stanek.jpg';
$video_MP4_ref = '/movie/video-stanek-MP4.mp4';
$video_WEBM_ref = '/movie/video-stanek-WEBM.mp4';
$stanek_ref = '/assets/stanek.png';


$shortName = 'kermi';
$letakAttributesClass = ['class' => 'letak-v-igelitce'];

$posters = [
        'Leták na přednášku',
        'Leták na soustružníka',
        'Inzerát na nic',
        'Jak nás nenajdete',
        
    ];
$letak = [];
foreach ($posters as $poster) {
    $letak[] = [
        'letakAttributes' => $letakAttributesClass +
        [
            'src' => Configuration::componentControler()['presenterFiles']."poster/$poster.jpg",
            'alt' => "$poster",
        ],
        'downloadAttributes' => [
            'href' => Configuration::componentControler()['presenterFiles']."poster/$poster.pdf",
            'download' => "$poster",
        ]
    ];
};

$buttonTitle = [
    'Pracovní pozice',
    'Náš program',
    'Chci navázat kontakt'
];

foreach ($buttonTitle as $title) {
    $buttony[] = [
        'text' => $title,
        'odkaz' => 'javascript: document.getElementById(\''.Configuration::componentControler()['prettyUrlCallable']($title).'\').scrollIntoView();',
    ];
};
    
$firma = [
    'nazev' => 'Kermi,&nbsp;s.r.o.',
    'videoAttributes' => [
        'poster' => Configuration::componentControler()['presenterFiles'].$shortName.$monitor_ref,
    ],
    'videoSourceSrc' => [
        ['src' => Configuration::componentControler()['presenterFiles'].$shortName.$video_MP4_ref, 'type' => 'video/mp4'],
        ['src' => Configuration::componentControler()['presenterFiles'].$shortName.$video_WEBM_ref, 'type' => 'video/webm'],
    ],
    'imgStankuAttributes' => [
        'src' => Configuration::componentControler()['presenterFiles'].$shortName.$stanek_ref,
        'alt' => 'stánek firmy',
    ],
    'socialniSiteIframe' => [
        [
            'ikonaSocialniSite' => 'facebook circle',
            'nazevSocialniSite' => 'Facebook',
            'btnClass' => 'btn-fb',
            'modalID' => 'modal_15',
            'iframe' => '<div class="fb-page" data-href="https://www.facebook.com/kermi.cz/" data-tabs="timeline" data-width="" data-height="" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/kermi.cz/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/kermi.cz/">Kermi - sprchové kouty, otopná tělesa</a></blockquote></div>',
            'odkazNaProfil' => 'https://www.facebook.com/kermi.cz/'
        ],
        [
            'ikonaSocialniSite' => 'linkedin circle',
            'nazevSocialniSite' => 'LinkedIn',
            'btnClass' => 'btn-in',
            'modalID' => 'modal_18',
            'iframe' => '<a href="https://cz.linkedin.com/company/kermi-s-r-o-/" target="_blank"><img src="'.Configuration::componentControler()['presenterFiles'].$shortName.'/assets/linkedin.png" alt="profil LinkednIn" height="" width="100%"/></a>',
            'odkazNaProfil' => 'https://cz.linkedin.com/company/kermi-s-r-o-/'
        ]
    ],
    'chat' => [
        'ikonaChatu' => 'chat circle',
        'text' => '',
        'odkaz' => ''
    ],
    'buttony' => $buttony,
    'letak' => $letak
];
