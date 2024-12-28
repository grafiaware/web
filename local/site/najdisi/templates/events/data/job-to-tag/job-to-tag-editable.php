<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
 
?>   

                <div class="field">
                     <?= Html::checkbox( $allTags , $checkedTags ); ?>
                </div>    

