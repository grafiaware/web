<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Html;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

$modalAtributy = [
    "id" => "modal_12",
    "class"=> ["ui tiny longer", "modal"]
];

$static_ref = '_www_vp_files/static/';
$firma = [
    'nazev' => 'Konplan s.r.o.',
    'videoAttributes' => [
        'poster' => $static_ref.'konplan/assets/monitor-na-stanek.jpg',
    ],
    'videoSourceSrc' => $static_ref.'konplan/movie/konplan_video.mp4',
    'imgStankuAttributes' => [
        'src' => 'images/stanek-bily.png', 
        'alt' => 'stánek First Company',
        'width' => '100%'
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
    ]
]

?>

<article class="paper">
    <section>
        <headline>
            <?= $this->insert(__DIR__.'/headline.php', $firma)?>
        </headline>
        <perex>
            <?php include "perex.php" ?> 
        </perex>
    </section>
    <section>    
        <?= $this->insert(__DIR__.'/content/stanek.php', $firma)?>
    </section>
</article>
