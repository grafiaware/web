<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */

    //    $readonly = 'readonly="1"';
    //    $disabled = 'disabled="1"';
        $readonly = '';
        $disabled = ''; 
?>

        <form class="ui huge form" action="" method="POST" >          
            
             <div class="three fields">                        
                <!-- <div class="field">
                <label>CompanyId</label>
                    <input < ?= $readonly ?> type="text" name="company-id" placeholder="" maxlength="10" value="< ?= isset($companyId)?  $companyId : '' ?>">
                </div>  -->
                
                <div class="field">
                   
                            <label>Jméno instituce</label>
                            <input <?= $readonly ?> type="text" name="institutionName" placeholder="" maxlength="100" value="<?= isset($name)?  $name : '' ?>">
                   
                </div>
                
                <div class="field"> 
                    
                    
                    
                    
                    <?php  if (isset($institutionTypeId) ) { ?>
                            <label>Typ instituce</label>
                            <input <?= $readonly ?> type="text" name="institutionType" placeholder=""  value="<?= isset($institutionTypeId)? $institutionType : '' ?>">
                    <?php } else { ?>
                            <?= Html::select("selectInstitutionType", "Jméno instituce:", $selectInstitutionType, [], []) ?>                      
                    <?php } ?> 
                            
                            
                    <div class="field">
                    <label>Stupeň: </label>
                    <input  type="number" name="stupen" placeholder="" min="1" max="20" value="<?= $stupen ?? '' ?>" >
                    </div>       
                            
                            
                            
                </div>
            </div>
            
            
            
            
            
                   

                <?php
                if($readonly === '') {
                ?>
                <div>
                    <?=
                     isset($institutionId) ?
                    "<button class='ui primary button' type='submit' formaction='events/v1//".$institutionId. "' > Uložit </button>" :
                    "<button class='ui primary button' type='submit' formaction='events/v1//' > Uložit </button>" ;                                   
                     ?>   
                    
                    <?=
                    isset($institutionId) ?
                    "<button class='ui primary button' type='submit' formaction='events/v1//".$institutionId."/remove' > Odstranit instituci </button>" :
                    "" ;
                    ?>                                                                                                         
                </div>
                <?php
                }
                ?>

        </form>           