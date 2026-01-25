<?php
use Site\ConfigurationCache;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */

$headline = 'Videonávody';
$perex = 'Podívejte se, jak se registrovat, přihlásit nebo jak vložit životopis k pracovní pozici u vybrané firmy';


$videoMp4Filename = ConfigurationCache::files()['@sitemovies'].'navod-MP4.m4v';
$videoMp4IsReadable = is_readable($videoMp4Filename);
$videoWebmFilename = ConfigurationCache::files()['@sitemovies'].'navod-WEBM.webm';
$videoWebmIsReadable = is_readable($videoWebmFilename);

$videonavody = [
    'videoAttributes' => [
        'poster' => '@sitemovies/navod-poster.jpg',
    ],
    'videoSourceSrc' => [
        $videoMp4IsReadable ? ['src' => $videoMp4Filename, 'type' => 'video/m4v'] : ['src' => "Not readable $videoMp4Filename", 'type' => 'video/m4v'],
        $videoWebmIsReadable ? ['src' => $videoWebmFilename, 'type' => 'video/webm'] : ['src' => "Not readable $videoMp4Filename", 'type' => 'video/m4v'],
    ],
]

?>
<article class="paper">
    <section>
        <headline>
            <?php include "headline.php" ?>
        </headline>
        <perex>
            <?php include "perex.php" ?>
        </perex>
    </section>
    <section>    
            <?= $this->insert(__DIR__.'/content/navody.php', $videonavody) ?>
    </section>
</article>
