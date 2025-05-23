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
$companyName = 'possehl';
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
    'nazev' => 'Possehl Electronics&nbsp;s.r.o.',
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
            'iframe' => '<div class="fb-page" data-href="https://www.facebook.com/PossehlElectronicsCzechRepublic/" data-tabs="timeline" data-width="" data-height="" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/PossehlElectronicsCzechRepublic/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/PossehlElectronicsCzechRepublic/">Possehl Electronics Czech Republic sro</a></blockquote></div>',
            'odkazNaProfil' => 'https://www.facebook.com/PossehlElectronicsCzechRepublic/'
        ]
    ],
    'chat' => [
        'ikonaChatu' => 'chat circle',
        'text' => '',
        'odkaz' => ''
    ],
    'buttony' => $buttony,
];
