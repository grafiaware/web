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
$companyName = 'akka';
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
    'nazev' => 'AKKA&nbsp;s.r.o.',
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
            'iframe' => '<div class="fb-page" data-href="https://www.facebook.com/AKKACzechRepublic/" data-tabs="timeline" data-width="" data-height="" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/AKKACzechRepublic/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/AKKACzechRepublic/">AKKA Czech Republic</a></blockquote></div>',
            'odkazNaProfil' => 'https://www.facebook.com/AKKACzechRepublic/'
        ],
        [
            'ikonaSocialniSite' => 'linkedin circle',
            'nazevSocialniSite' => 'LinkedIn',
            'btnClass' => 'btn-ig',
            'modalID' => 'modal_instagram',
            'iframe' => '<a href="https://www.linkedin.com/company/akka-technologies/" target="_blank"><img src="'.ConfigurationCache::files()['@presenter']."/".$companyName.'/assets/linkedin.png" alt="profil LinkednIn" height="" width="100%"/></a>',
            'odkazNaProfil' => 'https://www.linkedin.com/company/akka-technologies/'
        ],
        [
            'ikonaSocialniSite' => 'youtube',
            'nazevSocialniSite' => 'Youtube',
            'btnClass' => 'btn-yt',
            'modalID' => 'modal_youtube',
            'iframe' => '<a href="https://www.youtube.com/c/akkatechnologies/" target="_blank"><img src="'.ConfigurationCache::files()['@presenter']."/".$companyName.'/assets/youtube.png" alt="profil Youtube" height="" width="100%"/></a>',
            'odkazNaProfil' => 'https://www.youtube.com/c/akkatechnologies/'
        ]
    ],
    'chat' => [
        'ikonaChatu' => 'chat circle',
        'text' => '<p>Volejte Vandě Štěrbové</p>
                   <p>na telefon: 730 183 567</p>',
        'odkaz' => ''
    ],
    'buttony' => $buttony,
];
