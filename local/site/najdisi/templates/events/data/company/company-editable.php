<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

?>
                     
                <div class="field">
                    <label>Název firmy</label>
                    <input type="text" name="name" placeholder="" maxlength="250" value="<?= $name ?? '' ?>" required>
                    <span></span>
                </div>  
  