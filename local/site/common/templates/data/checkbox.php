<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Message;

use Pes\Text\Html;
/** @var PhpTemplateRendererInterface $this */

?>
        <form class="ui huge form" action="" method="POST" >

    <div class="ui styled fluid accordion">   

            <div class="active title">
                <i class="dropdown icon"></i>
                <?= $listHeadline ?>
            </div>     
            <div class="list active content">      
                <div class="field">
                     <?= Html::checkbox( $allCheckboxes , $checkedCheckboxes ); ?>
                </div>   
                <button class='ui primary button' type='submit' formaction='<?= "$componentRouteSegment" ?>' > Uložit </button>
            </div>            
    </div>