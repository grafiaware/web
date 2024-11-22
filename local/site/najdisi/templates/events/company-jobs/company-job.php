<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */        
    if ($editableData) {
        $readonly = '';
        $disabled = '';
    } else {
        $readonly = 'readonly';
        $disabled = 'disabled';
    }        
?>


        <form class="ui huge form" action="" method="POST" >           

            <div class="two fields">                        
                <div class="field">
                <label>Název pozice</label>
                    <input <?= $readonly ?> type="text" name="nazev" placeholder="" maxlength="120" value="<?= isset($nazev)?  $nazev : '' ?>" required>
                 </div>                 
                
                <div class="field">                                       
                    <?php
                    if($editableData) {
                    ?>                       
                        <?= Html::select( "pozadovane-vzdelani-stupen", "Požadované vzdělání:", 
                                      ["pozadovane-vzdelani-stupen"=> $pozadovaneVzdelaniStupen ?? '' ],  
                                      $selectEducations ??  [], 
                                      ['required' => true ],
                                      []) ?>  
                     <?php                      
                    }else{   ?> 
                        <?=  "Požadované vzdělání: <br/>"  .  
                             $selectEducations [$pozadovaneVzdelaniStupen] ?? ''   ?>
                    <?php                      
                     } 
                     ?> 
                </div>
            </div>
            <div class="two fields">
                <div class="field">
                    <label>Místo výkonu</label>
                    <input <?= $readonly ?> type="text" name="misto-vykonu" placeholder="" maxlength="45" 
                                            value="<?= isset($mistoVykonu)?  $mistoVykonu : '' ?>">
                </div>
                <div class="field">
                    <label>Popis pozice</label>
                    <input <?= $readonly ?> type="text" name="popis-pozice" placeholder="" maxlength="1000" 
                                            value="<?= isset($popisPozice)?  $popisPozice : '' ?>">
                </div>
            </div>   
            <div class="two fields">
                <div class="field">
                    <label>Požadujeme</label>
                    <input <?= $readonly ?> type="text" name="pozadujeme" placeholder="" maxlength="1000" 
                                            value="<?= isset($pozadujeme)?  $pozadujeme : '' ?>">
                </div>
                <div class="field">
                    <label>Nabízíme</label>
                    <input <?= $readonly ?> type="text" name="nabizime" placeholder="" maxlength="1000" 
                                            value="<?= isset($nabizime)?  $nabizime : '' ?>">
                </div>
            </div>      
               
            
               <?php
                if($editableData) {
                ?>
                <div>
                    <?=
                     isset($jobId) ?
                    "<button class='ui primary button' type='submit' formaction='events/v1/company/".$companyId."/job/". $jobId ."' > Uložit </button>" :
                    "<button class='ui primary button' type='submit' formaction='events/v1/company/".$companyId."/job' > Uložit (přidat) </button>" ;
                    ?>   
                </div>    
                <?php
                }
                ?>    
                    
            
                <?php
                if($editableList) {
                ?>   
                <div>
                    <?=
                     isset($jobId) ?
                    "<button class='ui primary button' type='submit' formaction='events/v1/company/".$companyId."/job/". $jobId ."/remove' > Odstranit job </button>" :
                    "" ;
                    ?>                                                                                                         
                </div>
                <?php
                }
                ?>    

        </form>           




