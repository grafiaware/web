<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;
?>
    <div>
    <div class="ui styled fluid accordion">        
        <div class="active title">
            <i class="dropdown icon"></i>
             Kontakty firmy :  <?= $companyName ?>   
        </div>                                 
        <div class="active content">      
            <?= $this->repeat(__DIR__.'/contact-editable.php', 
                              $companyContacts)  ?>

            <?php if ($addContact) { ?>                
                    Přidej kontakt firmy <?= $companyName ?>
                    <?= $this->insert( __DIR__.'/contact-editable.php', 
                                      ['companyId' => $companyId, 'editable' => true] ) ?>                                                                                 
            <?php  }  ?>    
        </div>            
    </div>
    </div>
