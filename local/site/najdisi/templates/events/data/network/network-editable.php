    <div class="network">
        <div class="field">
        <span class="text zadne-okraje" data-info="editable">
                            <a href="<?= $link ?? '' ?>" target="_blank"><i class="<?= $icon ?> circle icon"></i><?= $title ?></a>
        </span>
        </div>
        <div class="field">
            <input type="url" name="link[<?= $networkId ?>]" placeholder="odkaz" maxlength="200" value="<?= $link ?? '' ?>" >
        </div>
    </div>


<!--<div class="two fields">   
        <div class="field">
            <input type="text" name="icon" plac
        <div class="fieldeholder="název ikony" maxlength="28" required value="<?= $icon ?? '' ?>">
        </div>">                
            <input type="text" name="embedCodeTemplate" placeholder="vložte kód embedCodeTemplate" maxlength="120" required value="<?= $embedCodeTemplate ?? '' ?>">              
        </div>
    </div>-->
