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
            <?php  if (isset ($type) ) {   ?>   
                    <p>Typ:</p>
                    <div class="field">
                    <input readonly type="text" name="type"  value="<?= $type ?>" >
                    </div>
                    Name:   
                    <div class="field">
                    <input <?= $readonly ?> type="text" name="vzdelani" placeholder="" maxlength="100" value="<?= isset($name) ? $name : '' ?>">
                    </div>
            <?php  } else {   ?>    
                    <p>Typ:</p>
                    <div class="field">
                    <input <?= $readonly ?>  type="text" name="type"   value="" >
                    </div>
                    Name:  
                    <div class="field">
                    <input <?= $readonly ?> type="text" name="name" placeholder="" maxlength="100" value="<?= isset($name) ? $name : '' ?>">
                    </div>
            <?php  } ?>    
            </div>
    
        <div>                                                                                                                                
            <?=
            isset($type) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/contenttype/" . $type . "'> Uložit </button>" .
                "<button class='ui primary button' type='submit' formaction='events/v1/contenttype/" . $type . "/remove'> Odstranit  </button>" :
                
                "<button class='ui primary button' type='submit' formaction='events/v1/contenttype' > Uložit </button>" ;                
            ?>                                                                                                         
        </div>

    </form >