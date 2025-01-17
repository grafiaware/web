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

     
       
            <div class="ui grid stackable">
                <?php if($videoLink ?? false) { ?>
                    <div class="sixteen wide computer ten wide large screen ten wide widescreen column">
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
                    <?php
                    if ($companyNetworksUri ?? false) {
                        echo Html::tag('span', 
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
                <div class="sixteen wide column">
                    <div class="ui styled fluid accordion">  
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