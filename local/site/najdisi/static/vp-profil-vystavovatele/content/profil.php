<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */

?>
    <div class="profil">
        <div class="ui stackable centered grid">
            <div class="column">
                <div class="ui styled fluid accordion">
                    <?= $this->insert(__DIR__.'/profil/representative.php',  $representativeContext )  ?>
                </div>
                <br/>
            </div>
        </div>
        
        
        <?php include ConfigurationCache::eventTemplates()['templates']."presenter-job/content/job-list.php"; ?>

        <p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll">Náš program</p>
        <?php 
//         include 'profil/harmonogram.php'
        ?>

    </div>