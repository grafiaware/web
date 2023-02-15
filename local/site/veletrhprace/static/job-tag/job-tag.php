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
                <label>Typ pracovní pozice: </label>
                <input  type="text" name="tag" placeholder="" maxlength="45" value="<?= isset($tag) ? $tag : '' ?>" >
        </div>


        <div>                                                                                                                                                       
                    <?=
                     isset($tag) ?
                    "<button class='ui primary button' type='submit' formaction='events/v1/jobtag/".$tag. "' > Uložit </button>" :
                    "<button class='ui primary button' type='submit' formaction='events/v1/jobtag' > Uložit </button>" 
                    ?>                                                                                                                             
                    <?=
                     isset($tag) ?
                    "<button class='ui primary button' type='submit' formaction='events/v1/jobtag/" .$tag. "/remove' > Odstranit </button>" :
                    "" ;
                    ?>                                                                                                                                 
        </div>    
        

    </form >