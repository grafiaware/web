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
                <label>Institution Type</label>
                    <input <?= $readonly ?> type="text" name="institutionType" placeholder="" maxlength="45"  minlength="1"
                                            value="<?= $institutionType ??  '' ?>">
                </div>  
                
            </div>                
              
            <div>
                <?=
                isset($institutionTypeId) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/institutiontype/".$institutionTypeId . "' > Uložit </button>" :
                "<button class='ui primary button' type='submit' formaction='events/v1/institutiontype' > Uložit </button>" ;                                   
                 ?>                                                                                                                             
                <?=
                isset($institutionTypeId) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/institutiontype/".$institutionTypeId."/remove' > Odstranit</button>" :
                "" ;
                ?>                                                                                                         
            </div>
               

        </form>           