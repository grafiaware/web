<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */    

$imagesPath = ConfigurationCache::files()['@siteimages'];

if (!empty($videoLink)) {
    // Rozparsování URL
    $parsedUrl = parse_url($videoLink);
    $host = $parsedUrl['host'] ?? '';
    $path = $parsedUrl['path'] ?? '';
    $query = $parsedUrl['query'] ?? '';

    // Inicializace proměnných
    $embedYoutubeUrl = '';
    $embedVimeoUrl = '';

    // Zpracování pro YouTube
    if (strpos($host, 'youtube.com') !== false || strpos($host, 'youtu.be') !== false) {
        $videoId = '';

        if (strpos($host, 'youtube.com') !== false && !empty($query)) {
            // Dlouhá YouTube URL (youtube.com/watch?v=...)
            parse_str($query, $queryParams);
            $videoId = array_key_exists('v', $queryParams) ? $queryParams['v'] ?? '' : '';
        } elseif (strpos($host, 'youtu.be') !== false) {
            // Krátká YouTube URL (youtu.be/...)
            $videoId = ltrim($path, '/');
        }

        if (!empty($videoId)) {
            $embedYoutubeUrl = "https://www.youtube.com/embed/" . htmlspecialchars($videoId);
        }
    }

    // Zpracování pro Vimeo
    if (strpos($host, 'vimeo.com') !== false) {
        $videoId = strtok(ltrim($path, '/'), '?'); // Získání ID z cesty

        if (!empty($videoId)) {
            $embedVimeoUrl = "https://player.vimeo.com/video/" . htmlspecialchars($videoId); //napr: https: //vimeo.com/295529046?share=copy
        }
    }
}
?>
     
            <div class="ui grid stackable">
                <div class="sixteen wide tablet eight wide computer nine wide large screen column">
                    <img src=<?="$imagesPath/$exhibitionStand"?> alt="$imagesName" class="stanek-img"/>
                </div> 
                <div class="sixteen wide tablet eight wide computer seven wide large screen seven wide widescreen column">
                    <?php if($videoLink ?? false) { ?>
                        <p class="text tucne zadne-okraje">Video</p>
                        <div class="video-container">
                            <?php if (!empty($embedYoutubeUrl)): ?>
                                <iframe width="420" height="236" src="<?= $embedYoutubeUrl ?>" 
                                        frameborder="0" 
                                        allow="encrypted-media; picture-in-picture" 
                                        allowfullscreen>
                                </iframe>
                            <?php elseif (!empty($embedVimeoUrl)): ?>
                                <iframe width="420" height="236" src="<?= $embedVimeoUrl ?>" 
                                        frameborder="0" 
                                        allow="encrypted-media; picture-in-picture" 
                                        allowfullscreen>
                                </iframe>
                            <?php endif; ?>
                        </div>
                    <?php } ?>
                    <?php
                    if ($companyNetworksUri ?? false) {
                        
                        echo '<p class="text tucne zadne-okraje">Sledujte nás</p>' .
                        Html::tag('span', 
                            [
                                'class'=>'cascade',
                                'data-red-apiuri'=> $companyNetworksUri,
                            ]
                        );
//                        echo "<p>Sítě lze vybírat po prvním uložení informací o firmě.</p>";
                    } else {
                        echo "<p>Zadávání sociálních sítí bude spuštěno v krátké době.</p>";
                    }
                    ?>  
                </div>
                <div class="sixteen wide column">
                    <div class="ui basic styled fluid accordion">  
                        <?php if($introduction ?? false) { ?>
                                <div class="active title">   
                                    <i class="dropdown icon"></i>O nás
                                </div>
                                <div class="active content">
                                    <div class="ui grid">
                                        <div class="column">
                                            <div><?= $introduction ?? '' ?></div>
                                        </div>
                                    </div>
                                </div>
                        <?php } ?>
                        <?php if($positives ?? false) { ?>
                                <div class="title">   
                                    <i class="dropdown icon"></i>Proč k nám
                                </div>
                                <div class="content">
                                    <div class="ui grid">
                                        <div class="column">
                                            <div><?= $positives ?></div>
                                        </div>
                                    </div>
                                </div>
                        <?php } ?>
                        <?php if($social ?? false) { ?>
                                <div class="title">   
                                    <i class="dropdown icon"></i>Jak žijeme
                                </div>
                                <div class="content">
                                    <div class="ui grid">
                                        <div class="column">
                                            <div><?= $social ?></div>
                                        </div>
                                    </div>
                                </div>
                        <?php } ?>
                    </div>   
                </div>   
                <?php if($companyImages ?? false) { ?>
                <div class="sixteen wide column">
                    <img class="" alt="" src="<?= $companyImages?>" height="300" width="auto"/>
                </div>   
                <?php } ?>
            </div>