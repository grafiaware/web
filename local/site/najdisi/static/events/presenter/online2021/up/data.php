<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Site\ConfigurationCache;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\FriendlyUrl;
use Red\Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */


$monitor_ref = '/movies/monitor-stanek.jpg';
$video_MP4_ref = '/movies/video-stanek-MP4.mp4';
$video_WEBM_ref = '/movies/video-stanek-WEBM.webm';
$stanek_ref = '/assets/stanek.png';


$companyName = 'up';
$letakAttributesClass = ['class' => 'letak-v-igelitce'];


$buttonTitle = [
    'Pracovní pozice',
    'Náš program',
//    'Chatujte s námi (Eures)',
    'Kontakty'
];

$friendlyUrl = new FriendlyUrl();
foreach ($buttonTitle as $title) {
    $buttony[] = [
        'text' => $title,
        'odkaz' => 'javascript: document.getElementById(\''.$friendlyUrl->friendlyUrlText($title).'\').scrollIntoView();',
    ];
};

$firma = [
    'nazev' => 'Úřad práce&nbsp;ČR a&nbsp;EURES',
    'videoAttributes' => [
        'poster' => ConfigurationCache::files()['@presenter']."/".$companyName.$monitor_ref,
    ],
    'videoSourceSrc' => [
        ['src' => ConfigurationCache::files()['@presenter']."/".$companyName.$video_MP4_ref, 'type' => 'video/mp4'],
        ['src' => ConfigurationCache::files()['@presenter']."/".$companyName.$video_WEBM_ref, 'type' => 'video/webm'],
    ],
    'imgStankuAttributes' => [
        'src' => ConfigurationCache::files()['@presenter']."/".$companyName.$stanek_ref,
        'alt' => 'stánek firmy',
    ],
    'socialniSiteIframe' => [
        [
            'ikonaSocialniSite' => 'facebook circle',
            'nazevSocialniSite' => 'Facebook',
            'btnClass' => 'btn-fb',
            'modalID' => 'modal_facebook',
            'iframe' => '<div class="fb-page" data-href="https://www.facebook.com/uradprace.cr" data-tabs="timeline" data-width="" data-height="" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/uradprace.cr" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/uradprace.cr">Úřad práce ČR</a></blockquote></div>',
            'odkazNaProfil' => 'https://www.facebook.com/uradprace.cr'
        ]
    ],
    'chat' => [
        'ikonaChatu' => 'chat circle',
        'text' => '',
        'odkaz' => ''
    ],
    'buttony' => $buttony,
];
