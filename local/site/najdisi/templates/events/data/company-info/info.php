<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */    

if (!empty($videoLink)) {
    // Převod na embed URL
    $parsedUrl = parse_url($videoLink);
    parse_str($parsedUrl['query'], $queryParams);
    $videoId = $queryParams['v'];
    $embedUrl = "https://www.youtube.com/embed/" . $videoId;
} 
?>

     
       
            <div class="ui grid">
                <?php if($videoLink ?? false) { ?>
                    <div class="sixteen wide mobile ten wide computer column">
                        <p class="text tucne zadne-okraje">Video</p>
                        <div class="video-container">
                            <iframe width="444" height="250" src="<?php echo htmlspecialchars($embedUrl); ?>" 
                                    frameborder="0" 
                                    allow="encrypted-media; picture-in-picture" 
                                    allowfullscreen>
                            </iframe>
                        </div>
                    </div> 
                <?php } ?>
                <div class="sixteen wide column">
                    <?php if($introduction ?? false) { ?>
                        <div class="ui styled fluid accordion">  
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
                        </div>    
                    <?php } ?>
                    <?php if($positives ?? false) { ?>
                        <div class="ui styled fluid accordion">  
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
                        </div>   
                    <?php } ?>
                    <?php if($social ?? false) { ?>
                        <div class="ui styled fluid accordion">  
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
                        </div>   
                    <?php } ?>
                </div>   
                <?php if($companyImages ?? false) { ?>
                    <div class="sixteen wide column">
                        <img class="" alt="" src="<?= $companyImages?>" height="300" width="auto"/>
                    </div>   
                <?php } ?>
            </div>