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


############################
$companyName = 'konplan';
############################


$monitorFilename = ConfigurationCache::files()['@presenter']."/".$companyName.'/movies/monitor-stanek.jpg';
$monitorIsReadable = is_readable($monitorFilename);
$videoMp4Filename = ConfigurationCache::files()['@presenter']."/".$companyName.'/movies/video-stanek-MP4.mp4';
$videoMp4IsReadable = is_readable($videoMp4Filename);
$videoWebmFilename = ConfigurationCache::files()['@presenter']."/".$companyName.'/movies/video-stanek-WEBM.webm';
$videoWebmIsReadable = is_readable($videoWebmFilename);
$stanek_ref = '/assets/stanek.png';


$buttonTitle = [
    'Pracovní pozice',
    'Náš program',
    'Chci na online pohovor',
    'Chci navázat kontakt'
];

$friendlyUrl = new FriendlyUrl();
foreach ($buttonTitle as $title) {
    $buttony[] = [
        'text' => $title,
        'odkaz' => 'javascript: document.getElementById(\''.$friendlyUrl->friendlyUrlText($title).'\').scrollIntoView();',
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
        'src' => ConfigurationCache::files()['@presenter']."/".$companyName.$stanek_ref,
        'alt' => 'stánek firmy',
    ],
    'socialniSiteIframe' => [
        [
            'ikonaSocialniSite' => 'facebook circle',
            'nazevSocialniSite' => 'Facebook',
            'btnClass' => 'btn-fb',
            'modalID' => 'modal_facebook',
            'iframe' => '<div class="fb-page" data-href="https://www.facebook.com/KonplanCZ" data-tabs="timeline" data-width="" data-height="" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/KonplanCZ" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/KonplanCZ">Konplan</a></blockquote></div>',
            'odkazNaProfil' => 'https://www.facebook.com/KonplanCZ/'
        ],
        [
            'ikonaSocialniSite' => 'linkedin circle',
            'nazevSocialniSite' => 'LinkedIn',
            'btnClass' => 'btn-in',
            'modalID' => 'modal_linkedin',
            'iframe' => '<a href="https://www.linkedin.com/company/konplancz" target="_blank"><img src="'.ConfigurationCache::files()['@presenter']."/".$companyName.'/assets/linkedin.png" alt="profil LinkednIn" height="" width="100%"/></a>',
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
