<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Message;
/** @var PhpTemplateRendererInterface $this */

?>

        <?= $addHeadline ?? false ? "<p class='podnadpis'>$addHeadline</p>" : "" ?>
        <?= $infoText ?? false ? "<p class='ui blue segment'>$infoText</p>" : "" ?>
        <?php $formUid= uniqid(); ?>
        <form id="<?= $formUid ?>" class="ui big form" action="" method="POST" enctype="multipart/form-data" onKeyup="eventsEnableButtonsOnForm(event)" onChange="eventsEnableButtonsOnForm(event)">
                <?= $this->insert($fieldsTemplate, $fields  ?? []) ?>
            <!--buttons-->
            <div class="text okraje-dole">
                <?=
                    isset($actionSave) 
                    ? "<button disabled ".(isset($formUid) ? "id='edit_$formUid'" : "")." class='ui primary button' onClick='eventsDisableButtonsOnForm(event)'  type='submit' formaction='$actionSave'> ".($titleSave ?? 'Uložit změny')." </button>" : '';
                ?>
                <?=
                    isset($actionAdd) 
                    ? "<button disabled ".(isset($formUid) ? "id='add_$formUid'" : "")." class='ui primary button' type='submit' formaction='$actionAdd'> ".($titleAdd ?? 'Přidat')." </button>" : '';
                ?>
                <?=
                    isset($actionRemove) 
                    ? "<button ".(isset($formUid) ? "id='remove_$formUid'" : "")." class='ui primary button' type='submit' formaction='$actionRemove'> ".($titleAdd ?? 'Odstranit')." </button>" : "";
                ?>
                <?=
                    isset($actionSpecific) 
                    ? "<button ".(isset($formUid) ? "id='edit_$formUid'" : "")." class='ui primary button' type='submit' formaction='$actionSpecific'> $titleSpecific </button>" : '';
                ?>                   
                <?=
                    "<button ".(isset($formUid) ? "id='reset_$formUid'" : "")." style='display:none' class='ui secondary button' onClick='eventsResetButton(event)' type='reset'> Vrátit změny zpět </button>";
                ?>    
            </div>   
        </form>