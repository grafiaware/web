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
            <?php
                echo Html::tag('span', 
                    [
                        'class'=>'cascade',
                        'data-red-apiuri'=>"events/v1/data/job/$id/jobtotag",
                    ]
                );     
            ?>                 
        </div>
        <div class="content">
            <div class="two fields">                        
                <div class="field">
                    <p class="text tucne zadne-okraje">Místo výkonu:</p>
                    <p><?= $mistoVykonu ?? '' ?></p>
                </div>
                <div class="field">
                    <p class='text zadne-okraje tucne'>Požadované vzdělání: </p>
                    <p><?= $selectEducations [$pozadovaneVzdelaniStupen] ?? '' ?></p>
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




