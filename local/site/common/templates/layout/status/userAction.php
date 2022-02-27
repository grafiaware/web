<!--data generuje: StatusViewModel-->
<?php
use Red\Middleware\Redactor\Controler\UserActionControler;
?>

<form class="ui form" method="POST" action="">
    <div class="ui icon left pointing dropdown button">
        Akce
        <div class="menu">
            <div class="item header">
                <p><i class="user icon"></i><?= $userName ?></p>
            </div>
            <!--
            <button class="fluid ui primary labeled icon button" type="submit" name="app" value="rs" formtarget="_blank"
                formmethod="GET" formaction="red/v1/useraction/app/rs">
                <i class="edit outline large icon"></i>
                Redakční systém
            </button>
            <button class="fluid ui yellow labeled icon button" type="submit" name="app" value="edun" formtarget="_blank"
                formmethod="GET" formaction="red/v1/useraction/app/edun">
                <i class="file alternate outline large icon"></i>
                Katalog kurzů
            </button>
            -->
            <button class="fluid ui olive labeled icon button" type="submit" name="<?= UserActionControler::FORM_USER_ACTION_EDIT_MODE ?>" value="<?= empty($editContent) ? 1 : 0 ?>" formtarget="_self"
                formaction="red/v1/presentation/edit_mode">
                <i class="pencil alternate large icon"></i>
                <?= empty($editContent) ? "Zapnout inline editaci" : "Vypnout inline editaci" ?>
            </button>
        </div>
    </div>
</form>




