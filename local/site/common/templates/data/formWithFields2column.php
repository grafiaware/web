<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Message;
/** @var PhpTemplateRendererInterface $this */

?>

    <?= $addHeadline ?? false ? "<p class='podnadpis'>$addHeadline</p>" : "" ?>
    <?= $infoText ?? false ? "<p class='ui blue segment'>$infoText</p>" : "" ?>
    <?php $formUid= uniqid(); ?>
    <div class="sixteen wide column">
        <form id="<?= $formUid ?>" class="ui big form" action="" method="POST" onKeyup="eventsEnableButtonsOnForm(event)" onChange="eventsEnableButtonsOnForm(event)">
                <?= $this->insert($fieldsTemplate, $fields  ?? []) ?>
            <!--buttons-->
            <div class="text okraje-dole">
                <?=
                    isset($actionSave) 
                    ? "<button disabled ".(isset($formUid) ? "id='edit_$formUid'" : "")." class='ui primary button'onClick='eventsDisableButtonsOnForm(event)'  type='submit' formaction='$actionSave'> Uložit změny </button>" : '';
                ?>
                <?=
                    isset($actionAdd) 
                    ? "<button disabled ".(isset($formUid) ? "id='add_$formUid'" : "")." class='ui primary button' type='submit' formaction='$actionAdd'> Přidat </button>" : '';
                ?>
                <?=
                    isset($actionRemove) 
                    ? "<button ".(isset($formUid) ? "id='remove_$formUid'" : "")." class='ui primary button' type='submit' formaction='$actionRemove'> Odstranit </button>" : "";
                ?>
                <?=
                    "<button ".(isset($formUid) ? "id='reset_$formUid'" : "")." style='display:none' class='ui secondary button' onClick='eventsResetButton(event)' type='reset'> Vrátit změny zpět </button>";
                ?>
                <?=
                    isset($actionSpecific) 
                    ? "<button ".(isset($formUid) ? "id='edit_$formUid'" : "")." class='ui primary button' type='submit' formaction='$actionSpecific'> $titleSpecific </button>" : '';
                ?>                       
            </div>   
        </form>         
    </div>