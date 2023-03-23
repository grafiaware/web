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
            
            <div class="two fields">                                        
                <div class="field">                   
                    <label>Jméno instituce</label>
                    <input <?= $readonly ?> type="text" name="institutionName" placeholder="" maxlength="100" value="<?= isset($name)?  $name : '' ?>">                   
                </div>
                
                <div class="field">                                                    
                    <?= Html::select( "institutionTypeId", "Typ instituce:", $selectInstitutionTypeId ?? [] , 
                                     ["institutionTypeId" =>  $institutionTypeId ?? ''  ], []) ?>                   
                </div>               
                               
            </div>
            
     
            <?php
            if($readonly === '') {
            ?>
            <div>
                <?=
                 isset($institutionId) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/institution/".$institutionId. "' > Uložit </button>" :
                "<button class='ui primary button' type='submit' formaction='events/v1/institution' > Uložit </button>" ;                                   
                 ?>   

                <?=
                isset($institutionId) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/institution/".$institutionId."/remove' > Odstranit instituci </button>" :
                "" ;
                ?>                                                                                                         
            </div>
            <?php
            }
            ?>

        </form>           