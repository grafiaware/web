<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */        
    if ($editable) {
        $readonly = '';
        $disabled = '';
    } else {
        $readonly = 'readonly';
        $disabled = 'disabled';
    }        
?>

            <div class="two fields">                        
                <div class="field">
                <label>Název pozice</label>
                    <input <?= $readonly ?> type="text" name="nazev" placeholder="" maxlength="120" value="<?= $nazev ?? '' ?>" required>
                    <span></span>
                 </div>                 
                
                <div class="field">                    
                    <?php
                    if($editable) {
                    ?>                       
                        <?= Html::select( "pozadovane-vzdelani-stupen", "Požadované vzdělání:", 
                                      ["pozadovane-vzdelani-stupen"=> $pozadovaneVzdelaniStupen ?? '' ],  
                                      $selectEducations ??  [], 
                                      ['required' => true ],
                                      []) ?>  
                    <span></span>
                     <?php   
                     
                    }else{   ?> 
                        <?=  "<p class='text zadne-okraje tucne'>Požadované vzdělání: </p><p>"  .  
                             $selectEducations [$pozadovaneVzdelaniStupen] ."</p>" ?? ''   ?>
                    <?php                      
                     } 
                     ?> 
                </div>
            </div>
            <div class="two fields">
                <div class="field">
                    <label>Místo výkonu</label>
                    <input <?= $readonly ?> type="text" name="misto-vykonu" placeholder="" maxlength="45" 
                                            value="<?= $mistoVykonu ?? '' ?>">
                    <span></span>
                </div>
                <div class="field">
                    <label>Popis pozice</label>
                    <input <?= $readonly ?> type="text" name="popis-pozice" placeholder="" maxlength="1000" 
                                            value="<?= $popisPozice ?? '' ?>">
                    <span></span>
                </div>
            </div>   
            <div class="two fields">
                <div class="field">
                    <label>Požadujeme</label>
                    <input <?= $readonly ?> type="text" name="pozadujeme" placeholder="" maxlength="1000" 
                                            value="<?= $pozadujeme ?? '' ?>">
                    <span></span>
                </div>
                <div class="field">
                    <label>Nabízíme</label>
                    <input <?= $readonly ?> type="text" name="nabizime" placeholder="" maxlength="1000" 
                                            value="<?= $nabizime ?? '' ?>">
                    <span></span>
                </div>
            </div>             




