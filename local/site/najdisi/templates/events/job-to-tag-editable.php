<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

use Pes\Text\Text;
use Pes\Text\Html;



?>  
    <div>
        
    <div class="ui styled fluid accordion">                 
            
            <div class="active title">
                 <i class="dropdown icon"></i>
                ******* Přiřaďte typy k nabízeným pozicím firmy <?= $company->getName(); ?>
            </div>
            <div class="content">
                <?= $this->repeat(__DIR__.'/job-to-tag/job-to-tag.php',  $jobToTagies  )  ?>
            </div>                    
    </div>
    </div>
 
