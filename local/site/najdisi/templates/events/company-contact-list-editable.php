<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

use Pes\Text\Text;
use Pes\Text\Html;

?>

    <div class="ui styled fluid accordion">   

            <div class="active title">
                <i class="dropdown icon"></i>
                Kontakty firem
            </div>     
        
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/company-contacts/company-editable.php',  $context)  ?>               
            </div>            
    </div>

  