<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

use Pes\Core\Text\Text;
use Pes\Core\Text\Html;
?>     

    <div>
    <div class="ui styled fluid accordion">   

            <div class="active title">
                <i class="dropdown icon"></i>
                Nabízené pracovní pozice ve firmě  <?= isset($companyName) ? $companyName : ''  ?>
            </div>                        
        
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/company-jobs/company-job.php',  $jobs)  ?>

                            
            </div>            
    </div>
    </div>