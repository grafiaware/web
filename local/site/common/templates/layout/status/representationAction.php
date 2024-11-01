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
                <form class="ui form centered" method="POST" action="">
                    <div class="text okraje-vertical">
                    <?= Html::select(
                            RepresentationControler::FORM_REPRESENTATION_COMPANY_ID, 
                            "Zastupovaná firma", 
                            [RepresentationControler::FORM_REPRESENTATION_COMPANY_ID=> $selectedCompanyId],  
                            $idCompanyArray, 
                            ['required' => true ],
                            $placeholderValue) ?>  
                    </div> 
                    <div class="ui fluid large buttons">
                        <button class="ui positive button" type="submit" formtarget="_self" formaction="events/v1/representation">Odeslat</button>
                    </div>
                </form>
        </div>
    </div>



