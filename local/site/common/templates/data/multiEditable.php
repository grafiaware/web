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
            <form class="ui huge form" action="" method="POST"  onKeyup="eventsEnableButtonsOnForm(event)">
            <?= $this->insert($fieldsTemplate, $fields  ?? []) ?> 
            <?=
                isset($actionSave) 
                ? "<button class='ui primary button' type='submit' formaction='$actionSave' > Uložit </button>" : '';
            ?>            
            </form>     
        </div>            
    </div>
            