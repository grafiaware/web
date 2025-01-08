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
                    if ($dataRedApiUri) {
                        echo Html::tag('span', 
                            [
                                'class'=>'cascade',
                                'data-red-apiuri'=> $dataRedApiUri,
                            ]
                        );
                    } else {
                        echo "<p>Tagy lze k pozici vybírat po prvním uložení pozice.</p>";
                    }
                    ?>             
                </div><div class="grouped fields">       
                <div class="field">
                    <label>Místo výkonu</label>
                    <input type="text" name="misto-vykonu" placeholder="" maxlength="45" 
                                            value="<?= $mistoVykonu ?? '' ?>">
                    <span></span>
                </div>
                    <div class="field">                                          
                        <?= Html::select( "pozadovane-vzdelani-stupen", "Požadované vzdělání:", 
                                      ["pozadovane-vzdelani-stupen"=> $pozadovaneVzdelaniStupen ?? '' ],  
                                      $selectEducations ??  [], 
                                      ['required' => true , 'onChange'=>'eventsEnableButtonsOnInput(event)'],
                                      []) ?>  
                    </div>
                </div>
            </div>

            <div class="field">
                <label>Popis pozice</label>
                <input class="edit-userinput" type="text" name="popis-pozice" placeholder="" maxlength="1000" 
                                        value="<?= Text::esc_attr($popisPozice) ?? '' ?>">
                <span></span>
            </div>

            <div class="two fields">
                <div class="field">
                    <label>Požadujeme</label>
                    <input class="edit-userinput" type="text" name="pozadujeme" placeholder="" maxlength="1000" 
                                            value="<?= Text::esc_attr($pozadujeme) ?? '' ?>">
                    <span></span>
                </div>
                <div class="field">
                    <label>Nabízíme</label>
                    <input class="edit-userinput" type="text" name="nabizime" placeholder="" maxlength="1000" 
                                            value="<?= Text::esc_attr($nabizime) ?? '' ?>">
                    <span></span>
                </div>
            </div>             

