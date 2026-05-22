<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Core\Text\Text;
use Pes\Core\Text\Html;

/** @var PhpTemplateRendererInterface $this */        
 
?>

    <label><b>Nahraný soubor</b></label>                    
        <div class="field">
            <p><b> <?= $filename ?? '---' ?></b></p>                                                                 
        </div>                              
     
                                 