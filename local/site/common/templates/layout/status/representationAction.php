<?php
use Pes\Text\Html;
use Events\Middleware\Events\Controler\RepresentationControler;
?>


<!--vyber firmy je modalni okno, jde zavrit pouze pri stisku buttonu 'odeslat' v js initElements-->
    <button class="ui page icon button btn-vyberFirmy">
        Výběr firmy
    </button>

    <div class="ui mini page modal transition hidden vyberFirmy">
        <div class="content">
                <p class="text velky zadne-okraje"><i class="user icon"></i><?= $loginName ?></p>
                <form class="ui form centered" method="POST" action="events/v1/representation">
                    <div class="text okraje-vertical">
                    <?= Html::select(
                            RepresentationControler::FORM_REPRESENTATION_COMPANY_ID, 
                            "Zastupovaná firma", 
                            [RepresentationControler::FORM_REPRESENTATION_COMPANY_ID=> $selectedCompanyId],  
                            $idCompanyArray, 
                            ['required' => true, 'onchange'=>'this.form.submit()'],
                            $placeholderValue) ?>  
                    </div> 
                    <div class="ui toggle checkbox">
                        <input type="checkbox" name="<?= RepresentationControler::FORM_REPRESENTATION_EDIT_DATA ?>" <?= $editData ? "checked" : "" ?> onchange="this.form.submit()">
                        <label><?= $editData ? "Vypnout editaci údajů" : "Zapnout editaci údajů"?></label>
                    </div>
<!--                    <div class="ui fluid large buttons">
                        <button class="ui positive button" type="submit" formtarget="_self" formaction="events/v1/representation">Odeslat</button>
                    </div>
                    <button class="fluid ui olive labeled icon button" type="submit" name="<?= RepresentationControler::FORM_REPRESENTATION_EDIT_DATA ?>" value="<?= empty($edit) ? 1 : 0 ?>" formtarget="_self"
                        formaction="events/v1/representation/edit_mode">
                        <i class="pencil alternate icon"></i>
                        <?= empty($edit) ? "Zapnout editaci údajů" : "Vypnout editaci údajů" ?>
                    </button>                    -->
                </form>
        </div>
    </div>



