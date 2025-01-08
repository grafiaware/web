<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */    


// Převod na embed URL
$parsedUrl = parse_url($videoLink);
parse_str($parsedUrl['query'], $queryParams);
$videoId = $queryParams['v'];
$embedUrl = "https://www.youtube.com/embed/" . $videoId;
    
?>

    <div class="ui styled fluid accordion">   
       
        <div class="title">
            <i class="dropdown icon"></i> 
                <?= $nazev ?? '' ?> 
        </div>
        <div class="content">
            <div class="ui grid">    
                <div class="row">
                    <div class="sixteen wide column">
                        <p class="text tucne zadne-okraje">O nás</p>
                        <p><?= $introduction ?? '' ?></p>
                    </div>   
                </div>
                <?php if($embedUrl?? false) { ?>
                <div class="row">                
                    <div class="ten wide column">
                        <p class="text tucne zadne-okraje">Video</p>
                        <div class="video-container">
                            <iframe width="444" height="250" src="<?php echo htmlspecialchars($embedUrl); ?>" 
                                    frameborder="0" 
                                    allow="encrypted-media; picture-in-picture" 
                                    allowfullscreen>
                            </iframe>
                        </div>
                    </div> 
                </div> 
                <?php } ?>
                <div class="row">                
                    <div class="eight wide column">
                        <p class="text tucne zadne-okraje">Proč k nám</p>
                        <p><?= $positives ?? '' ?></p>
                    </div>                 
                    <div class="eight wide column">
                        <p class="text tucne zadne-okraje">Jak žijeme</p>
                        <p><?= $social ?? '' ?></p>
                    </div>
                </div>
            </div>   
        </div>        
    </div> 




