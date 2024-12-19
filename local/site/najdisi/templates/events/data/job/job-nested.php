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

    <div class="accordion">   
        <div class="title">
            <i class="dropdown icon"></i> <?= $nazev ?? '' ?> 
            <?php /*if($checkedTagsText){*/ ?> <span class="ui big red tag label tag-list"> lala <?php /*= implode(', ',array_keys($checkedTagsText) ); */?></span><?php /* } */?> 
        </div>
        <div class="content">
            <div class="two fields">                        
                <div class="field">
                    <p class='text zadne-okraje tucne'>Požadované vzdělání: </p>
                </div>
                <div class="field">
                    <p><?= $selectEducations [$pozadovaneVzdelaniStupen] ?? '' ?></p>
                </div>
            </div>
            <div class="two fields">                        
                <div class="field">
                    <p class="text tucne zadne-okraje">Místo výkonu:</p>
                </div>
                <div class="field">
                    <p><?= $mistoVykonu ?? '' ?></p>
                </div>
            </div>
            <div class="two fields">                        
                <div class="field">
                    <p class="text tucne zadne-okraje">Popis pozice:</p>
                </div>                
                <div class="field">
                    <p><?= $popisPozice ?? '' ?></p>
                </div>                
            </div>
            <div class="two fields">                        
                <div class="field">
                    <p class="text tucne zadne-okraje">Požadujeme:</p>
                    <p><?= $pozadujeme ?? '' ?></p>
                </div>                 
                <div class="field">
                    <p class="text tucne zadne-okraje">Nabízíme:</p>
                    <p><?= $nabizime ?? '' ?></p>
                </div>
            </div>
        </div>   
    </div>        




