<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */
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
