<?php

use Events\Middleware\Events\Controler\FilterControler;

use Pes\Text\Html;


?>

    <div>
        <p class="podnadpis okraje">Nastavte hodnoty pro výběr nabízených pracovních pozic:</p>
    </div>
    <div id="toggleFilter" class="ui big black button <?= $isFilterVisible ?? false ? 'active' : '' ?>">
        <i class="<?= $isFilterVisible ?? false ? 'close' : 'filter' ?> icon"></i> 
        <?= $isFilterVisible ?? false ? 'Skrýt filtr' : 'Zobrazit filtr' ?>
    </div>
    
    <div id="filterSection" class="ui segment">
        <form class="ui big form" action="" method="POST" > 
            <div class="field">
                <?= Html::select( 
                    FilterControler::FILTER_COMPANY, 
                    "Firma:",  
                    [ FilterControler::FILTER_COMPANY => $selectCompanyId ],    
                    $selectCompanies ??  [] , 
                    []    // ['required' => true ],                        
                ) ?> 
            </div>

            <div class="field">
                 <div>Typ hledané pozice: </div>
                 <?= Html::checkbox( $filterCheckboxLabelsAndNameValuePairs , $filterCheckboxData ); ?>
            </div>

            <div>      
                <button class='ui secondary button' type='submit' 
                        formaction='events/v1/filterjob'> Vyhledat pozice podle filtru</button>
                <button class='ui primary button' type='submit' 
                        formaction='events/v1/cleanfilterjob'> Vyčistit filtr a zobrazit vše</button>
            </div>                                    
        </form>
    </div>