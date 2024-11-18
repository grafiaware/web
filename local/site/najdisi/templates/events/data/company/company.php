<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */
?>

        <form class="ui huge form" action="" method="POST" >

            <div class="two fields">                        
                <div class="field">
                <label>Název firmy</label>
                    <input readonly type="text" name="name" placeholder="" maxlength="250" value="<?= $name ?? '' ?>" required >
                 </div>  
            </div>                 
        </form>           