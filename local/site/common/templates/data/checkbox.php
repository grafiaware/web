<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Message;

use Pes\Text\Html;
/** @var PhpTemplateRendererInterface $this */
?>
     
                <div class="field">
                     <?= Html::checkbox( $allCheckboxes , $checkedCheckboxes ); ?>
                </div>   
