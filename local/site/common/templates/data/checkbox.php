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
            <form class="ui huge form" action="" method="POST"  onKeyup="eventsEnableButtonsOnForm(event)">
                <div class="field">
                     <?= Html::checkbox( $allCheckboxes , $checkedCheckboxes ); ?>
                </div>   
            <?=
                isset($actionSave) 
                ? "<button class='ui primary button' type='submit' formaction='$actionSave' > Uložit </button>" : '';
            ?>            
            </form>     
        </div>            
    </div>