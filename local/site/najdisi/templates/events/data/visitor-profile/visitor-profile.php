<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */
 if ($editable) {
        $readonly = '';
        $disabled = '';
    } else {
        $readonly = 'readonly';
        $disabled = 'disabled';
    }   
?> 


<div class="profil">
        <div class="ui stackable centered grid">
            <div class="column">
                <div class="ui styled fluid accordion">
                    <?php 
                        include 'osobni-udaje-profil.php'; ?>
                    
                    <?= '';//$this->insert(__DIR__.'/profil/igelitka.php', $igelitka); ?>
                    <?php  /* include 'profil/harmonogram.php' */ ?>
                </div>
                <br/>
            </div>
        </div>

    </div>


