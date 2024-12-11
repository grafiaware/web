<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */        
if ($editable) {
        $readonly = '';
        $disabled = '';
    } else {
        $readonly = 'readonly';
        $disabled = 'disabled';
    }        
?>
    <?= ($addHeadline ?? TRUE) ? "<p>$addHeadline</p>" : "" ?>

    <label><b>Nahraný soubor</b></label>                    
    <form class="ui huge form"  method="POST" >
        <div class="field">
            <p><b> <?= $filename ?? '---' ?></b></p>                                                                 
        </div>                              
    </form>                  
     
                                 