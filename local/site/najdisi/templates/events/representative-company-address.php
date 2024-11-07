<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

use Pes\Text\Text;
use Pes\Text\Html;



?>     
    <div>
    <div class="ui styled fluid accordion">       
                        
            <div class="active title">
                <i class="dropdown icon"></i>
                Firma  <?= $name ?>    
            </div> 
            
            <div class="active content">      
                <?= $this->insert(__DIR__.'/company-address/company-address.php',  $companyAddress)  ?>                               
            </div>
    </div>
    </div>
