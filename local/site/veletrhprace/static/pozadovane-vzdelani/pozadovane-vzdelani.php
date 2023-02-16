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

        <div class="field">
                <br/>
                <label>Stupeň: </label>
                <input  type="text" name="stupen" placeholder="" maxlength="11" value="<?= isset($stupen) ? $stupen : '' ?>" >
                
                <br/>
                <label>Vzdělání: </label>
                <input  type="text" name="vzdelani" placeholder="" maxlength="100" value="<?= isset($vzdelani) ? $vzdelani : '' ?>" >

        </div>


        <div>                                                                                                                                                       
                <?=
                 isset($stupen) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/pozadovanevzdelani/".$stupen. "' > Uložit </button>" :
                "<button class='ui primary button' type='submit' formaction='events/v1/pozadovanevzdelani' > Uložit </button>" 
                ?>                                                                                                                             
                <?=
                 isset($tag) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/jobtag/" .$stupen. "/remove' > Odstranit </button>" :
                "" ;
                ?>                                                                                                                                 
        </div>    
        

    </form >