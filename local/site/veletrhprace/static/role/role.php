<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
 

?>
<div>
    <form class="ui huge form" action="" method="POST" >               
        
        <div class="field">
            Role:
            <?php  if (isset ($role) ) {   ?>
                    <input  type="text" name="role" placeholder="" maxlength="50" value="<?= isset($role) ? $role: ''?>">
            <?php  } else {   ?>    
                    <input  type="text" name="role" placeholder="" maxlength="50" 
                            pattern="[A-Za-z0-9]]"  title="Jen alfanumerické znaky.- bez hacku"
                            required value="<?= isset($role) ? $role : ''?>">
            <?php  } ?>            
        </div>
        <div>                                                                                                                                
            <?=
            isset($role) ?
                "<button class='ui secondery button' type='submit' formaction='auth/v1/role/" .$role . "'>Ulozit</button>
                 <button class='ui primary button' type='submit' formaction='auth/v1/role/" .$role . "/remove'>Odstranit</button>" :
                "<button class='ui secondery button' type='submit' formaction='auth/v1/role' >Uložit</button>" ;                
            ?>                                                                                                         
        </div>
    </form >
  </div>

   