<?php
use Red\Middleware\Redactor\Controler\PresentationActionControler;
?>

    <input type="hidden" name="hidden" value="hidden" /> 
    <!--hidden proměná zajišťuje vznik $_POST pole v PHP - bez ní pokud není checkbox checked, proměnná checkbox inputu neexistuje a protože jiná proměnná ve formuláři není, nevznikne $_POST-->
   <button class="ui page icon button btn-modalActions">
        <p><b>Akce editora</b></p>
        <p class="">
            <i class="user icon"></i><?= $loginName ?>
            <i class="<?= $editContent ? "green" : "red"?> power off icon"></i> <?= ($editContent ? "edituje " : "needituje ") ?>
        </p>
    </button>
       
    <div class="ui mini page modal transition hidden modalActions">
        <i class="white close icon"></i>
        <div class="content">
            <p class="text velky zadne-okraje"><i class="user icon"></i><?= $loginName ?></p>

            <form class="ui form" method="POST" action="red/v1/presentation/editoraction">
                <div class="ui toggle checkbox">
                    <input id="prepnout-editaci" type="checkbox" name="<?= PresentationActionControler::FORM_PRESENTATION_EDIT_MODE ?>" <?= $editContent ? "checked" : "" ?> onchange="this.form.submit()">
                    <label for="prepnout-editaci"><?= $editContent ? "Vypnout inline editaci" : "Zapnout inline editaci"?></label>
                </div>
            </form>
        </div>
    </div>



