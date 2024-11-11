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
                 Kontakty firmy :  <?= $name ?>   
            </div>                        
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/company-contacts/company-contact-editable.php', 
                                  $companyContacts)  ?>

                <?php if ($editable) { ?>                
                    <div class="active title">
                        <i class="dropdown icon"></i>
                        Přidej kontakt firmy
                    </div>  
                    <div class="active content">     
                        <?= $this->insert( __DIR__.'/company-contacts/company-contact-editable.php', 
                                          ['companyId' => $idCompany, 'editable' => $editable] ) ?>                                                                                 
                    </div>               
                <?php  }  ?>    
            </div>            
    </div>
    </div>
