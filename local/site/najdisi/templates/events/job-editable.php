<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */

?>     
    <div>
    <div class="ui styled fluid accordion">       
                        
            <div class="active title">
                <i class="dropdown icon"></i>
                - 1 pozice z firmy <?= $companyName ?>   
            </div> 
            
            <div class="active content">      
                <?= $this->insert(__DIR__.'/job/job.php', $job)  ?>                               
            </div>
    </div>
    </div>
