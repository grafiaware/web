<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Core\Text\Text;
use Pes\Core\Text\Html;

/** @var PhpTemplateRendererInterface $this */
 
?>   

                <div class="field">
                     <?= Html::checkbox( $allTags , $checkedTags ); ?>
                </div>    

