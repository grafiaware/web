<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Message;
/** @var PhpTemplateRendererInterface $this */

?>

    <div class="ui styled fluid accordion">   
        <div class="active title">
            <i class="dropdown icon"></i>
            <?= $listHeadline ?>
        </div>     
        <div class="list active content">      
            <form class="ui big form" action="" method="POST"  onKeyup="eventsEnableButtonsOnForm(event)">
            <?= $this->insert($fieldsTemplate, $fields  ?? []) ?> 
                <?=
                    isset($actionSave) 
                    ? "<button disabled ".(isset($formUid) ? "id='edit_$formUid'" : "")." class='ui primary button' onClick='eventsDisableButtonsOnForm(event)'  type='submit' formaction='$actionSave'> ".($titleSave ?? 'Uložit změny')." </button>" : '';
                ?>     
            </form>     
        </div>            
    </div>
            