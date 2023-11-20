<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
 

?>

    <form class="ui huge form" action="" method="POST" >               
        
        <div class="field">
            Role:
            <?php  if (isset ($role) ) {   ?>
                    <input readonly  type="text" name="role" placeholder="" maxlength="50" value="<?= isset($role) ? $role : '' ?>">
            <?php  } else {   ?>    
                    <input  type="text" name="role" placeholder="" maxlength="50" required value="<?= isset($role) ? $role : '' ?>">
            <?php  } ?>            
        </div>

        <div>                                                                                                                                
            <?=
            isset($role) ?
                "<button class='ui primary button' type='submit' formaction='events/v1/role/" .$role . "/remove'> Odstranit  </button>" :
                "<button class='ui primary button' type='submit' formaction='events/v1/role' > Uložit </button>" ;                
            ?>                                                                                                         
        </div>

    </form >