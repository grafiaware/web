<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

use Pes\Core\Text\Text;
use Pes\Core\Text\Html;
?>     

    <div>
    <div class="ui styled fluid accordion">                                       
           
            <div class="active title">
                <i class="dropdown icon"></i>
                Nabízené pracovní pozice ve firmě  <?= isset($companyName) ? $companyName : '' ?>
            </div>                      
        
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/company-jobs/company-job.php',  $jobs)  ?>
              
                
                <?php
                    if($editableList) {
                ?>  
                
                <div class="active title">
                    <i class="dropdown icon"></i>
                    Přidej pracovní pozici
                </div>  
                <div class="active content">     
                    <?= $this->insert( __DIR__.'/company-jobs/company-job.php',
                            [
                             'companyId' => $companyId, 
                             'selectEducations' =>  $selectEducations,
                             'editableData' => true,
                             'editableList' => true   
                            ] 
                            ) ?>                                                                                 
                </div> 
                
                <?php                      
                 } 
                ?> 
                
                
            </div>            
    </div>
    </div>