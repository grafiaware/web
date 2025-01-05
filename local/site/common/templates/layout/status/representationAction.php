<?php
use Pes\Text\Html;
use Events\Middleware\Events\Controler\RepresentationControler;

$hasRepreCompany = (bool) $companyName ?? false;
$mustSelectCompany = !$isAdministrator && (count($idCompanyArray)>1);
?>


<!--vyber firmy je modalni okno, jde zavrit pouze pri stisku buttonu 'odeslat' v js initElements-->
    <button class="ui page icon button btn-vyberFirmy">
        <p><b>Akce reprezentanta</b></p>
        <p class="">
            <i class="user icon"></i><?= $loginName ?>
            <div class="ui divider"></div>
                <?php if($mustSelectCompany) {?>
                    <i class="briefcase icon"></i><?= $hasRepreCompany ? "zastupuje $companyName" : "Nemá vybranou firmu" ?>
                    <div class="ui divider"></div>
                <?php } ?>
            <i class="<?= $editData ? "green" : "red"?> power off icon"></i> <?= ($editData ? "edituje " : "needituje ") ?>
        </p>
    </button>

    <div class="ui mini page modal transition hidden vyberFirmy">
        <i class="white close icon"></i>
        <div class="content">
                <p class="text velky zadne-okraje"><i class="user icon"></i><?= $loginName ?></p>
                <form class="ui form centered" method="POST" action="events/v1/representation">
                    <div class="text okraje-vertical">
                    <?php if($mustSelectCompany) {
                            echo Html::select(
                            RepresentationControler::FORM_REPRESENTATION_COMPANY_ID, 
                            "Zastupovaná firma", 
                            [RepresentationControler::FORM_REPRESENTATION_COMPANY_ID=> $selectedCompanyId],  
                            $idCompanyArray, 
                            ['required' => true, 'onchange'=>'this.form.submit()'],
                            $placeholderValue);
                        }
                    ?>  
                    </div> 
                    <?php if (!$mustSelectCompany OR $hasRepreCompany) {?>
                    <div class="ui toggle checkbox">
                        <input id="prepnout-editaci-repre" type="checkbox" name="<?= RepresentationControler::FORM_REPRESENTATION_EDIT_DATA ?>" <?= $editData ? "checked" : "" ?> onchange="this.form.submit()">
                        <label for="prepnout-editaci-repre"><?= $editData ? "Vypnout editaci údajů" : "Zapnout editaci údajů"?></label>
                    </div>
                    <?php } ?>
<!--                    <div class="ui fluid large buttons">
                        <button class="ui positive button" type="submit" formtarget="_self" formaction="events/v1/representation">Odeslat</button>
                    </div>
                    <button class="fluid ui olive labeled icon button" type="submit" name="<?= RepresentationControler::FORM_REPRESENTATION_EDIT_DATA ?>" value="<?= empty($editData) ? 1 : 0 ?>" formtarget="_self"
                        formaction="events/v1/representation/edit_mode">
                        <i class="pencil alternate icon"></i>
                        <?= empty($editData) ? "Zapnout editaci údajů" : "Vypnout editaci údajů" ?>
                    </button>    
-->
                </form>
                
        </div>
    </div>



