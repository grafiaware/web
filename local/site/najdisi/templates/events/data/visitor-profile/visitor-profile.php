<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */
  
?> 


<div class="profil">
        <div class="ui stackable centered grid">
            <div class="column">
                <div class="ui styled fluid accordion">
                    <?=
                        $this->insert( __DIR__.'/osobni-udaje-profil.php', $profileData  )    
                        .
                        $this->insert(__DIR__.'/osobni-soubory-profil.php', $documents ); 

                    ?>
                    
                    <?= '';//$this->insert(__DIR__.'/profil/igelitka.php', $igelitka); ?>
                    <?php  /* include 'profil/harmonogram.php' */ ?>
                </div>
                <br/>
            </div>
        </div>

    </div>


