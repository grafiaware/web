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

    <form class="ui huge form" action="" method="POST" >               
        
        <div class="field">
            Typ pracovní pozice:
            <?php  if (isset ($tagId) ) {   ?>
                    <input readonly  type="text" name="tag" placeholder="" maxlength="45" value="<?= isset($tag) ? $tag : '' ?>">
            <?php  } else {   ?>    
                    <input <?= $readonly ?> type="text" name="tag" placeholder="" maxlength="45" value="<?= isset($tag) ? $tag : '' ?>">
            <?php  } ?>            
        </div>

        <div>                                                                                                                                
            <?=
            isset($tagId) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/jobtag/" .$tagId . "/remove'> Odstranit  </button>" :
                "<button class='ui primary button' type='submit' formaction='events/v1/jobtag' > Uložit </button>" ;                
            ?>                                                                                                         
        </div>

    </form >