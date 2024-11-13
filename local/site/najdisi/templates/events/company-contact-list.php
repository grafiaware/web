<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;
?>


    <div>
    <div class="ui styled fluid accordion">                               
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/company-contacts/company-contact.php',  $companies)  ?>

                               
            </div>            
    </div>
    </div>
