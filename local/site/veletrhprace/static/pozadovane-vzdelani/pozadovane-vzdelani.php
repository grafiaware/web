<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
 

    //    $readonly = 'readonly="1"';
    //    $disabled = 'disabled="1"';
        $readonly = '';
        $disabled = ''; 

?>
    <br>
    <form class="ui huge form" action="" method="POST" >                       
      
            <div class="fields">  
            <?php  if (isset ($stupen) ) {   ?>   
                    <p>Stupeň (číslo):</p>
                    <div class="field">
                    <input readonly type="text" name="stupen"  value="<?= $stupen ?>" >
                    </div>
                    Vzděláni:   
                    <div class="field">
                    <input <?= $readonly ?> type="text" name="vzdelani" placeholder="" maxlength="100" value="<?= isset($vzdelani) ? $vzdelani : '' ?>">
                    </div>
            <?php  } else {   ?>    
                    <p>Stupeň (číslo):</p>
                    <div class="field">
                    <input <?= $readonly ?>  type="number" name="stupen"   value="" >
                    </div>
                    Vzděláni:  
                    <div class="field">
                    <input <?= $readonly ?> type="text" name="vzdelani" placeholder="" maxlength="100" value="<?= isset($vzdelani) ? $vzdelani : '' ?>">
                    </div>
            <?php  } ?>    
            </div>
    
        <div>                                                                                                                                
            <?=
            isset($stupen) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/vzdelani/" . $stupen . "'> Uložit </button>" .
                "<button class='ui primary button' type='submit' formaction='events/v1/vzdelani/" . $stupen . "/remove'> Odstranit  </button>" :
                
                "<button class='ui primary button' type='submit' formaction='events/v1/vzdelani' > Uložit </button>" ;                
            ?>                                                                                                         
        </div>

    </form >