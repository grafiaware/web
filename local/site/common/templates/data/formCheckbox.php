<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Message;

use Pes\Text\Html;
/** @var PhpTemplateRendererInterface $this */
?>
     


    <div class="ui styled fluid accordion">   
        <div class="active title">
            <i class="dropdown icon"></i>
            <?= $listHeadline ?>
        </div>     
        <div class="list active content">     
            <?= $fieldsInfoText ?? false ? "<p class='ui blue segment'>$fieldsInfoText</p>" : "" ?>            
            <form class="ui big form" action="" method="POST"  onKeyup="eventsEnableButtonsOnForm(event)">
                <div class="field">
                     <?= Html::checkbox( $allCheckboxes , $checkedCheckboxes ); ?>
                </div>   
                <?=
                    isset($actionSave) 
                    ? "<button disabled ".(isset($formUid) ? "id='edit_$formUid'" : "")." class='ui primary button' onClick='eventsDisableButtonsOnForm(event)'  type='submit' formaction='$actionSave'> ".($titleSave ?? 'Uložit změny')." </button>" : '';
                ?>    
            </form>     
        </div>            
    </div>