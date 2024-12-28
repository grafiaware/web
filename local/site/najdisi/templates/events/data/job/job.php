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

    <div class="ui styled fluid accordion">   
        <div class="title">
            <i class="dropdown icon"></i> <?= $nazev ?? '' ?> 
            <?php
                echo Html::tag('span', 
                    [
                        'class'=>'cascade',
                        'data-red-apiuri'=>"events/v1/data/job/$jobId/jobtotag",
                    ]
                );     
            ?>
        </div>
        <div class="content">
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
                    <p class='text zadne-okraje tucne'>Požadované vzdělání: </p>
                </div>
                <div class="field">
                    <p><?= $selectEducations [$pozadovaneVzdelaniStupen] ?? '' ?></p>
                </div>
            </div>
            <div class="two fields">                        
                <div class="field">
                    <p class="text tucne zadne-okraje">Místo výkonu:</p>
                </div>
                <div class="field">
                    <p><?= $mistoVykonu ?? '' ?></p>
                </div>
            </div>
            <div class="two fields">                        
                <div class="field">
                    <p class="text tucne zadne-okraje">Popis pozice:</p>
                </div>                
                <div class="field">
                    <p><?= $popisPozice ?? '' ?></p>
                </div>                
            </div>
            <div class="two fields">                        
                <div class="field">
                    <p class="text tucne zadne-okraje">Požadujeme:</p>
                    <p><?= $pozadujeme ?? '' ?></p>
                </div>                 
                <div class="field">
                    <p class="text tucne zadne-okraje">Nabízíme:</p>
                    <p><?= $nabizime ?? '' ?></p>
                </div>
            </div>
        </div>   
    </div>        




