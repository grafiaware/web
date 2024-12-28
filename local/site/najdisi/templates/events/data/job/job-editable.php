<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */        
        $readonly = '';
        $disabled = '';      
?>

            <div class="two fields">                        
                <div class="field">
                <label>Název pozice</label>
                    <input type="text" name="nazev" placeholder="" maxlength="120" value="<?= $nazev ?? '' ?>" required>
                    <span></span>
                 </div>                 
                
                <div class="field">                                          
                        <?= Html::select( "pozadovane-vzdelani-stupen", "Požadované vzdělání:", 
                                      ["pozadovane-vzdelani-stupen"=> $pozadovaneVzdelaniStupen ?? '' ],  
                                      $selectEducations ??  [], 
                                      ['required' => true , 'onChange'=>'eventsEnableButtonsOnInput(event)'],
                                      []) ?>  
                    <span></span>
                </div>
                <div class="field">
                    <label>Místo výkonu</label>
                    <input type="text" name="misto-vykonu" placeholder="" maxlength="45" 
                                            value="<?= $mistoVykonu ?? '' ?>">
                    <span></span>
                </div>
            </div>
                <div class="field">
                    <label>Popis pozice</label>
                    <input class="edit-userinput" type="text" name="popis-pozice" placeholder="" maxlength="1000" 
                                            value="<?= $popisPozice ?? '' ?>">
                    <span></span>
                </div>
            </div>   
            <div class="two fields">
                <div class="field">
                    <label>Požadujeme</label>
                    <span></span>
                </div>
                <div class="field">
                    <label>Nabízíme</label>
                    <span></span>
                </div>
            </div>             





                    <div class="edit-representative borderDance" name="pozadujeme" required><?= $pozadujeme ?? '' ?></div>
                    <input class="edit-userinput" type="text" name="pozadujeme" placeholder="" maxlength="1000" 
                                            value="<?= $pozadujeme ?? '' ?>">
