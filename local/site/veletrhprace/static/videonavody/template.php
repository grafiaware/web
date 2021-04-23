<?php
use Site\Configuration;

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

$headline = 'Videonávody';
$perex = 'Podívejte se, jak se registrovat, přihlásit nebo jak vložit životopis k pracovní pozici u vybrané firmy';


$videoMp4Filename = '_www_vp_files/movies/navod-MP4.m4v';
$videoMp4IsReadable = is_readable($videoMp4Filename);
$videoWebmFilename = '_www_vp_files/movies/navod-WEBM.webm';
$videoWebmIsReadable = is_readable($videoWebmFilename);

$videonavody = [
    'videoAttributes' => [
        'poster' => 'images/videonavod-foto-velke.jpg',
    ],
    'videoSourceSrc' => [
        $videoMp4IsReadable ? ['src' => $videoMp4Filename, 'type' => 'video/m4v'] : null,
        $videoWebmIsReadable ? ['src' => $videoWebmFilename, 'type' => 'video/webm'] : null,
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
