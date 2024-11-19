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
                 Firma  <?= $companyName ?> 
            </div>
            <div class="content">
                <?= $this->repeat(__DIR__.'/job-to-tag/job-to-tag-editable.php',  $jobToTagies  )  ?>
            </div>                    
    </div>
    </div>
 
