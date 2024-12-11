<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

    if ($editable ?? false) {
        $readonly = '';
        $disabled = '';
    } else {
        $readonly = 'readonly';
        $disabled = 'disabled';
    }   
?>
                     
                <div class="eight wide field">
                    <label>Název firmy</label>
                    <input <?= $readonly ?> type="text" name="name" placeholder="" maxlength="250" value="<?= $name ?? '' ?>" required >
                </div>  
  