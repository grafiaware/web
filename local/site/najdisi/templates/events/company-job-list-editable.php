<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

use Pes\Text\Text;
use Pes\Text\Html;
?>     

    <div>
    <div class="ui styled fluid accordion">                                       
           
            <div class="active title">
                <i class="dropdown icon"></i>
                Nabízené pracovní pozice ve firmě  <?= isset($companyName) ? $companyName : '' ?>
            </div>                      
        
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/company-jobs/company-job.php',  $jobs)  ?>
              
                
                <div class="active title">
                    <i class="dropdown icon"></i>
                    Přidej pracovní pozici
                </div>  
                <div class="active content">     
                    <?= $this->insert( __DIR__.'/company-jobs/company-job.php',
                            [
                             'companyId' => $companyId, 
                             'selectEducations' =>  $selectEducations,
                             'editable' => $editable
                            ] 
                            ) ?>                                                                                 
                </div>   
                
            </div>            
    </div>
    </div>