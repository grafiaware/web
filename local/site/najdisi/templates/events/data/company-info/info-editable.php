<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */        
 
?>
<div class="ui styled fluid accordion">   
        <div class="active title">
            <i class="dropdown icon"></i> 
            Informace o firmě
        </div>
    <div class="active content">
       
            <div class="field">
                <label>O nás</label>
                <textarea class="edit-userinput" type="text" name="introduction" placeholder="" maxlength="1000"><?= $introduction ?? '' ?></textarea>
            </div>
            <div class="field">
                <label>Video</label>
                <input id="youtubeUrlQQQ" type="url" name="videolink" placeholder="https://www.youtube.com/watch?v=... | https://vimeo.com/123456789" maxlength="150" 
                                        value="<?= $videoLink ?? '' ?>">
                <p id="message"></p>
            </div>
                        <div class="field">
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
                </div>
            <div class="two fields">
                <div class="field">
                    <label>Proč k nám</label>
                    <textarea class="edit-userinput" type="text" name="positives" placeholder="" maxlength="1000"><?= $positives ?? '' ?></textarea>
                </div>
                <div class="field">
                    <label>Jak žijeme</label>
                    <textarea class="edit-userinput" type="text" name="social" placeholder="" maxlength="1000"><?= $social ?? '' ?></textarea>
                </div>
            </div>
        
    </div>
</div>
