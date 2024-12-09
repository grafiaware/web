<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
if ($editable) {
        $readonly = '';
        $disabled = '';
    } else {
        $readonly = 'readonly';
        $disabled = 'disabled';
    }   
?>   
                        <?php if ( $editable ) { ?>
                            <div class="field">
                                 <?= Html::checkbox( $allTags , $checkedTags ); ?>
                            </div>    

                            <?php }else { ?>
                                <div class="field">                                                                                               
                                        <?= implode(', ',array_keys($checkedTagsText) ); ?>                                 
                                </div>                         
                            <?php } ?>
