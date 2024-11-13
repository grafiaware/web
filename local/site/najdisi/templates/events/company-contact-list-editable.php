<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;
?>
    <div>
    <div class="ui styled fluid accordion">        
                                
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/company-contacts/company-contact-editable.php', 
                                  $companies)  ?>

                <?php if ($addContact) { ?>                
                    <div class="active title">
                        <i class="dropdown icon"></i>
                        Přidej kontakt firmy
                    </div>  
                    <div class="active content">     
                        <?= $this->insert( __DIR__.'/company-contacts/company-contact-editable.php', 
                                          ['companyId' => $idCompany, 'name'=>$name, 'editable' => true] ) ?>                                                                                 
                    </div>               
                <?php  }  ?>    
            </div>            
    </div>
    </div>
