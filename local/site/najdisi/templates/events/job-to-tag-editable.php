<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

use Pes\Core\Text\Text;
use Pes\Core\Text\Html;

/** @var PhpTemplateRendererInterface $this */
?>  
    <div>
        
    <div class="ui styled fluid accordion">                 
            
            <div class="active title">
                 <i class="dropdown icon"></i>      
                 Firma  <?= $companyName ?> 
            </div>
            <div class="content">
                <?= $this->repeat(__DIR__.'/data/job-tag/job-to-tag-editable.php',  $jobToTagies  )  ?>
            </div>                    
    </div>
    </div>
 
