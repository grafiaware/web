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
            <?php  if (isset ($type) ) {  /*stary*/ ?>   
                    <p>Typ obsahu:</p>
                    <div class="field">
                    <input <?= $readonly ?> type="text" name="type"  required  maxlength="45"  value="<?= $type ?>" >
                    </div>
                    Name:   
                    <div class="field">
                    <input <?= $readonly ?> type="text" name="name" placeholder="" maxlength="45" value="<?= isset($name) ? $name : '' ?>">
                    </div>
            <?php  } else {         /*novy*/ ?>    
                    <p>Typ:</p>
                    <div class="field">
                    <input <?= $readonly ?>  type="text" name="type"   required  maxlength="45"  value="" >
                    </div>
                    Name:  
                    <div class="field">  
                    <input <?= $readonly ?> type="text" name="name" placeholder="" maxlength="45"  value= ""  >
                    </div>
            <?php  } ?>    
            </div>
    
        <div>                                                                                                                                
            <?=
            isset($type) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/eventcontenttype/" . $id . "'> Uložit </button>" .
                "<button class='ui primary button' type='submit' formaction='events/v1/eventcontenttype/" . $id . "/remove'> Odstranit  </button>" :
                
                "<button class='ui primary button' type='submit' formaction='events/v1/eventcontenttype' > Uložit </button>" ;                
            ?>                                                                                                         
        </div>

    </form >