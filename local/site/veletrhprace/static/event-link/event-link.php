<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */

    //    $readonly = 'readonly="1"';
    //    $disabled = 'disabled="1"';
        $readonly = '';
        $disabled = ''; 
?>

        <form class="ui huge form" action="" method="POST" >          
            <div class="field"> 
                    <label>políčko Show - Zobraz:</label>  
                    <?= Html::checkbox( ["Zobraz" => ['show'=> 1 ] ] , 
                                        ['show'=> $show  ] ) ?>
            </div>   
            
            <div class="two fields">                                        
                <div class="field">                   
                    <label>Href - Odkaz:</label>
                    <input <?= $readonly ?> type="text" name="href" placeholder="" maxlength="255" required value="<?=  $href  ?>">                   
                </div>
                
                <div class="field">                                                    
                    <?= Html::select( "eventLinkPhaseId", "Fáze události:", $selectEventLinkPhase ?? [] , 
                                     ["eventLinkPhaseId" =>  $eventLinkPhaseIdFk ?? '' ], ['required' => true ]) ?>                   
                </div>               
                               
            </div>
            
     
            <?php
            if($readonly === '') {
            ?>
            <div>
                <?=
                isset($eventLinkId) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/eventlink/".$eventLinkId. "' > Uložit </button>" :
                "<button class='ui primary button' type='submit' formaction='events/v1/eventlink' > Uložit </button>" ;                                   
                 ?>   

                <?=
                isset($eventLinkId) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/eventlink/".$eventLinkId."/remove' > Odstranit odkaz </button>" :
                "" ;
                ?>                                                                                                         
            </div>
            <?php
            }
            ?>

        </form>           