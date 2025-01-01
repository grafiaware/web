<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */        
 
?>

            <div class="field">
                <label>Název pozice</label>
                <input type="text" name="nazev" placeholder="" maxlength="120" value="<?= $nazev ?? '' ?>" required>
            </div>  

            <div class="two fields">                        
                <div class="field">
                    <?php
                        echo Html::tag('span', 
                            [
                                'class'=>'cascade',
                                'data-red-apiuri'=>"events/v1/data/job/$id/jobtotag",
                            ]
                        );     
                    ?>                    
                </div><div class="grouped fields">       
                <div class="field">
                    <label>Místo výkonu</label>
                    <input type="text" name="misto-vykonu" placeholder="" maxlength="45" 
                                            value="<?= $mistoVykonu ?? '' ?>">
                    <span></span>
                </div>                <div class="field">                                          
                        <?= Html::select( "pozadovane-vzdelani-stupen", "Požadované vzdělání:", 
                                      ["pozadovane-vzdelani-stupen"=> $pozadovaneVzdelaniStupen ?? '' ],  
                                      $selectEducations ??  [], 
                                      ['required' => true , 'onChange'=>'eventsEnableButtonsOnInput(event)'],
                                      []) ?>  
                </div>
                </div>
            </div>

            <div class="two fields">

                <div class="field">
                    <label>Popis pozice</label>
                    <input class="edit-userinput" type="text" name="popis-pozice" placeholder="" maxlength="20" 
                                            value="<?= $popisPozice ?? '' ?>">
                    <span></span>
                </div>
            </div>

            <div class="two fields">
                <div class="field">
                    <label>Požadujeme</label>
                    <input class="edit-userinput" type="text" name="pozadujeme" placeholder="" maxlength="1000" 
                                            value="<?= $pozadujeme ?? '' ?>">
                    <span></span>
                </div>
                <div class="field">
                    <label>Nabízíme</label>
                    <input class="edit-userinput" type="text" name="nabizime" placeholder="" maxlength="1000" 
                                            value="<?= $nabizime ?? '' ?>">
                    <span></span>
                </div>
            </div>             





                    <!--<div class="edit-representative borderDance" name="pozadujeme" required><?= '';$pozadujeme ?? '' ?></div>-->
