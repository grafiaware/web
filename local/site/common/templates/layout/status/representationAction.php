<?php
use Pes\Text\Html;
use Events\Middleware\Events\Controler\RepresentationControler;
?>

    <div class="ui icon left pointing dropdown button">
        výběr firmy
        <div class="menu">
            <div class="text nastred">
                <p><i class="user icon"></i><?= $loginName ?></p>
            </div>
            <form class="ui form" method="POST" action="">
                <?= Html::select(
                        RepresentationControler::FORM_REPRESENTATION_COMPANY_ID, 
                        "Zastupovaná firma", 
                        [RepresentationControler::FORM_REPRESENTATION_COMPANY_ID=> $selectedCompanyId ?? ''],  
                        $idCompanyArray, 
                        ['required' => true ],
                        $placeholderValue) ?>  
                <button class="fluid ui olive labeled icon button" type="submit"
                        formtarget="_self"
                    formaction="events/v1/representation">
                    <i class="pencil alternate icon"></i>
                    Odeslat
                </button>            
            </form>        
            <span class="item">
        
            </span>

        </div>
    </div>



