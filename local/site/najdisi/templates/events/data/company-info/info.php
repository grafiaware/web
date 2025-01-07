<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */        
    
?>

    <div class="ui styled fluid accordion">   
       
        <div class="title">
            <i class="dropdown icon"></i> 
                <?= $nazev ?? '' ?> 
        </div>
        <div class="content">
            <div class="ui grid">       
                <div class="row">
                <div class="ten wide column">
                    <p class="text tucne zadne-okraje">O nás</p>
                    <p><?= $introduction ?? '' ?></p>
                </div>   
                <div class="six wide column">
                    <p class="text tucne zadne-okraje">Video</p>
                    <p><?= $videoLink ?? '' ?></p>
                </div> 
                </div>
                <div class="eight wide column">
                    <p class="text tucne zadne-okraje">Proč k nám</p>
                    <p><?= $positives ?? '' ?></p>
                </div>                 
                <div class="eight wide column">
                    <p class="text tucne zadne-okraje">Jak žijeme</p>
                    <p><?= $social ?? '' ?></p>
                </div>
            </div>
        </div>   
    </div>        




