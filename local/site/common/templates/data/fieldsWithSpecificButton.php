<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Message;
/** @var PhpTemplateRendererInterface $this */
?>
        <?= $infoText ?? false ? "<p class='ui blue segment'>$infoText</p>" : "" ?>

        <?= $this->insert($fieldsTemplate, $fields  ?? []) ?>     

        <?php $formUid= uniqid(); ?>
        <form id="<?= $formUid ?>" class="ui big form" action="" method="POST" onKeyup="eventsEnableButtonsOnForm(event)" onChange="eventsEnableButtonsOnForm(event)">
            <!--buttons-->
            <div class="text okraje-dole">
                <?=
                    isset($actionSpecific) 
                    ? "<button ".(isset($formUid) ? "id='edit_$formUid'" : "")." class='ui primary button' type='submit' formaction='$actionSpecific'> $titleSpecific </button>" : '';
                ?>                            
            </div>   
        </form>