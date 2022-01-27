<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Site\Configuration;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\FriendlyUrl;
use Red\Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

############################
$shortName = 'valeo';
############################


$monitorFilename = Configuration::files()['presenter'].$shortName.'/movies/monitor-stanek.jpg';
$monitorIsReadable = is_readable($monitorFilename);
$videoMp4Filename = Configuration::files()['presenter'].$shortName.'/movies/video-stanek-MP4.mp4';
$videoMp4IsReadable = is_readable($videoMp4Filename);
$videoWebmFilename = Configuration::files()['presenter'].$shortName.'/movies/video-stanek-WEBM.webm';
$videoWebmIsReadable = is_readable($videoWebmFilename);
$stanek_ref = '/assets/stanek.png';



$buttonTitle = [
    'Pracovní pozice',
    'Náš program',
//    'Chci na online pohovor',
    'Chci navázat kontakt'
];

foreach ($buttonTitle as $title) {
    $buttony[] = [
        'text' => $title,
        'odkaz' => 'javascript: document.getElementById(\''.Configuration::componentController()['prettyUrlCallable']($title).'\').scrollIntoView();',
    ];
};

$firma = [
    'nazev' => 'Valeo Autoklimatizace&nbsp;k.s.',
    'videoAttributes' => [
        'poster' => $monitorIsReadable ? $monitorFilename : "",
    ],
    'videoSourceSrc' => [
        $videoMp4IsReadable ? ['src' => $videoMp4Filename, 'type' => 'video/mp4'] : null,
        $videoWebmIsReadable ? ['src' => $videoWebmFilename, 'type' => 'video/webm'] : null,
    ],
    'imgStankuAttributes' => [
        'src' => Configuration::files()['presenter'].$shortName.$stanek_ref,
        'alt' => 'stánek firmy',
    ],
    'socialniSiteIframe' => [
        [
            'ikonaSocialniSite' => 'facebook circle',
            'nazevSocialniSite' => 'Facebook',
            'btnClass' => 'btn-fb',
            'modalID' => 'modal_facebook',
            'iframe' => '<div class="fb-page" data-href="https://www.facebook.com/Valeo.Group/" data-tabs="timeline" data-width="" data-height="" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/Valeo.Group/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/Valeo.Group/">Valeo</a></blockquote></div>',
            'odkazNaProfil' => 'https://www.facebook.com/Valeo.Group/'
        ],
        [
            'ikonaSocialniSite' => 'linkedin circle',
            'nazevSocialniSite' => 'LinkedIn',
            'btnClass' => 'btn-in',
            'modalID' => 'modal_linkedin',
            'iframe' => '<a href="https://www.linkedin.com/company/valeo" target="_blank"><img src="'.Configuration::files()['presenter'].$shortName.'/assets/linkedin.png" alt="profil LinkednIn" height="" width="100%"/></a>',
            'odkazNaProfil' => 'https://www.linkedin.com/company/valeo'
        ]
    ],
    'chat' => [
        'ikonaChatu' => 'chat circle',
        'text' => '<p>Chatujte s námi přes Google Meet</p>
                   <p>klikněte na odkaz: <a href="meet.google.com/isr-uuma-axh" target="_blank">meet.google.com/isr-uuma-axh</a></p>
                   <p>nebo se připojte přes telefon: 234 610 000 <br/> s PINEM: 978 240 881 8303#</p>',
        'odkaz' => '<a class="ui button" href="meet.google.com/isr-uuma-axh" target="_blank">Jít na odkaz Google Meet</a>'
    ],
    'buttony' => $buttony,
];
