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


############################
$shortName = 'konplan';
############################


$monitorFilename = Configuration::componentControler()['presenterFiles'].$shortName.'/assets/monitor-stanek.jpg';
$monitorIsReadable = is_readable($monitorFilename);
$videoMp4Filename = Configuration::componentControler()['presenterFiles'].$shortName.'/movie/video-stanek-MP4.mp4';
$videoMp4IsReadable = is_readable($videoMp4Filename);
$videoWebmFilename = Configuration::componentControler()['presenterFiles'].$shortName.'/movie/video-stanek-WEBM.mp4';
$videoWebmIsReadable = is_readable($videoWebmFilename);
$stanek_ref = '/assets/stanek.png';


$buttonTitle = [
    'Pracovní pozice',
    'Náš program',
    'Chci na online pohovor',
    'Chci navázat kontakt'
];

foreach ($buttonTitle as $title) {
    $buttony[] = [
        'text' => $title,
        'odkaz' => 'javascript: document.getElementById(\''.Configuration::componentControler()['prettyUrlCallable']($title).'\').scrollIntoView();',
    ];
};

$firma = [
    'nazev' => 'Konplan&nbsp;s.r.o.',
    'videoAttributes' => [
        'poster' => $monitorIsReadable ? $monitorFilename : "",
    ],
    'videoSourceSrc' => [
        $videoMp4IsReadable ? ['src' => $videoMp4Filename, 'type' => 'video/mp4'] : null,
        $videoWebmIsReadable ? ['src' => $videoWebmFilename, 'type' => 'video/webm'] : null,
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
            'iframe' => '<div class="fb-page" data-href="https://www.facebook.com/KonplanCZ" data-tabs="timeline" data-width="" data-height="" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/KonplanCZ" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/KonplanCZ">Konplan</a></blockquote></div>',
            'odkazNaProfil' => 'https://www.facebook.com/KonplanCZ/'
        ],
        [
            'ikonaSocialniSite' => 'linkedin circle',
            'nazevSocialniSite' => 'LinkedIn',
            'btnClass' => 'btn-ig',
            'modalID' => 'modal_16',
            'iframe' => '<a href="https://www.linkedin.com/company/konplancz" target="_blank"><img src="'.Configuration::componentControler()['presenterFiles'].$shortName.'/assets/linkedin.png" alt="profil LinkednIn" height="" width="100%"/></a>',
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
];
