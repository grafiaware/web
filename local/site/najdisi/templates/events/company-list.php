<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

use Pes\Text\Text;
use Pes\Text\Html;

?>

    <div class="ui styled fluid accordion">   

            <div class="active title">
                <i class="dropdown icon"></i>
                Firmy (companies)
            </div>     
        
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/company/company.php',  $companies)  ?>
                <?php
                if($editable) {
                ?>
                <div class="active title">
                    <i class="dropdown icon"></i>
                    Přidej firmu
                </div>  
                <div class="active content">     
                    <?= $this->insert( __DIR__.'/company/company.php') ?>                                                                                 
                </div> 
                <?php
                }
                ?>                
            </div>            
    </div>

  