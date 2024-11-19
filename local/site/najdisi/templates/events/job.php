<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

use Pes\Text\Text;
use Pes\Text\Html;



?>     
    <div>
    <div class="ui styled fluid accordion">       
                        
            <div class="active title">
                <i class="dropdown icon"></i>
                - pozice ve firmě <?= $name ?>    
            </div> 
            
            <div class="active content">      
                <?= $this->insert(__DIR__.'/job/job.php',  $compadress)  ?>                               
            </div>
    </div>
    </div>
