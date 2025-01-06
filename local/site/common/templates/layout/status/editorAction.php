<?php
use Red\Middleware\Redactor\Controler\PresentationActionControler;
?>

<form class="ui form" method="POST" action="red/v1/presentation/editoraction">
    <input type="hidden" name="hidden" value="hidden" /> 
    <!--hidden proměná zajišťuje vznik $_POST pole v PHP - bez ní pokud není checkbox checked, proměnná checkbox inputu neexistuje a protože jiná proměnná ve formuláři není, nevznikne $_POST-->
    <div class="ui icon pointing dropdown button">
        <p><b>Akce editora</b></p>
        <p class="">
            <i class="user icon"></i><?= $loginName ?>
            <i class="<?= $editContent ? "green" : "red"?> power off icon"></i> <?= ($editContent ? "edituje " : "needituje ") ?>
        </p>
        <div class="menu">
            <div class="text nastred">
                <p><i class="user icon"></i><?= $loginName ?></p>
            </div>
            <div class="item">
                <div class="ui toggle checkbox">
                    <input id="prepnout-editaci" type="checkbox" name="<?= PresentationActionControler::FORM_PRESENTATION_EDIT_MODE ?>" <?= $editContent ? "checked" : "" ?> onchange="this.form.submit()">
                    <label for="prepnout-editaci"><?= $editContent ? "Vypnout inline editaci" : "Zapnout inline editaci"?></label>
                </div>

<!--                <i class="dropdown icon"></i>
                Nastavení menu
                <div class="menu">
                    <div class="item"><a href="">Nezavírat menu (zapnout/vypnout)</a></div>
                    <div class="item"><a href="">Nastavit home page</a></div>
                    <div class="item">
                        <i class="dropdown icon"></i>
                        Něco dalšího
                        <div class="menu">
                            <div class="item disabled"><a href="">Vnořená 1</a></div>
                            <div class="item"><a href="">Vnořená 2</a></div>
                        </div>
                    </div>
                    <div class="item"><a href="">Nějaká další možnost</a></div>
                </div>-->
            </div>
        </div>
    </div>
</form>



