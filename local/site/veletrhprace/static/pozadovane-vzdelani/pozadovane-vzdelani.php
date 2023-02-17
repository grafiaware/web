<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
 

//        $readonly = 'readonly="1"';
//       $disabled = 'disabled="1"';
//        $readonly = '';
//        $disabled = ''; 

?>

    <form class="ui huge form" action="" method="POST" >

        <div class="two fields">              
                <div class="field">
                <label>Stupeň: </label>
                <input  type="number" name="stupen" placeholder="" min="1" max="20" value="<?= $stupen ?? '' ?>" >
                </div>
                
                <div class="field">               
                <label>Vzdělání: </label>
                <input  type="text" name="vzdelani" placeholder="" maxlength="100" value="<?= $vzdelani ?? '' ?>" >
                </div>

        </div>


        <div>                                                                                                                                                       
                <?=
                 isset($stupen) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/pozadovanevzdelani/".$stupen. "' > Uložit </button>" :
                "<button class='ui primary button' type='submit' formaction='events/v1/pozadovanevzdelani' > Uložit </button>" 
                ?>                                                                                                                             
                <?=
                 isset($stupen) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/pozadovanevzdelani/" .$stupen. "/remove' > Odstranit </button>" :
                "" ;
                ?>                                                                                                                                 
        </div>    
        

    </form >