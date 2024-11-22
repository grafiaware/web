<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */
?>

    <div class="ui styled fluid accordion">   

            <div class="active title">
                <i class="dropdown icon"></i>
                Firmy (companies)
            </div>     
        
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/company-editable.php',  $companies)  ?>
                <div class="active title">
                    <i class="dropdown icon"></i>
                    Přidej firmu
                </div>  
                <div class="active content">     
                    <?= $this->insert( __DIR__.'/company-editable.php') ?>                                                                                 
                </div>                
            </div>            
    </div>


  