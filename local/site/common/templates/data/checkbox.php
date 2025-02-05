<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Message;

use Pes\Text\Html;
/** @var PhpTemplateRendererInterface $this */
?>
     


    <div class="accordion">   
        <div class="active title">
            <i class="dropdown icon"></i>
            <?= $listHeadline ?? ''?>
        </div>     
        <div class="list active content">      
                <div class="field">
                     <?= Html::checkbox( $allCheckboxes , $checkedCheckboxes ); ?>
                </div>      
        </div>            
    </div>