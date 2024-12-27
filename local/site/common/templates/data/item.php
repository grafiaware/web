<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Message;
/** @var PhpTemplateRendererInterface $this */
?>

        <?= $addHeadline ?? false ? "<p>$addHeadline</p>" : "" ?>
        <?php $formUid= uniqid(); ?>
        <form id="<?= $formUid ?>" class="ui huge form" action="" method="POST" onKeyup="eventsEnableButtonsOnForm(event)">
            <!--<div class="two fields">-->   
                <?= $this->insertIf(!($editable ?? false), $fieldsTemplate, $fields  ?? [], __DIR__.'/noData.php') ?>
                <?= $this->insertIf($editable ?? false, $fieldsTemplate, $fields  ?? []) ?>
            <!--</div>-->                
            <!--buttons-->
            <div class="text okraje-dole">
                <?=
                $editable ?? false ? 
                    (isset($id) 
                    ?
                        "<button disabled ".(isset($formUid) ? "id='edit_$formUid'" : "")." class='ui primary button' type='submit' formaction='$componentRouteSegment/$id'> Uložit změny </button>"
                    :
                        "<button disabled ".(isset($formUid) ? "id='add_$formUid'" : "")." class='ui primary button' type='submit' formaction='$componentRouteSegment'> Přidat </button>" 
                    )        
                : "";
                ?>
                <?=
                ($editable ?? false) && ($remove ?? false) ? "<button ".(isset($formUid) ? "id='remove_$formUid'" : "")." class='ui primary button' type='submit' formaction='$componentRouteSegment/$id/remove'> Odstranit </button>" : "";
                ?>
                <?=
                ($editable ?? false) ? "<button ".(isset($formUid) ? "id='reset_$formUid'" : "")." style='display:none' class='ui secondary button' onClick='eventsResetButton(event)' type='reset'> Vrátit změny zpět </button>" : "";
                ?>
            </div>   
        </form>         