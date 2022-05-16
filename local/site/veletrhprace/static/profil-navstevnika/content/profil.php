<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

?>
    <div class="profil">
        <div class="ui stackable centered grid">
            <div class="column">
                <div class="ui styled fluid accordion">
                    <?php if( $role!=='presenter') {include ConfigurationCache::componentController()['templates'].'visitor-data/osobni-udaje-profil.php';} ?>
                    <?= '';//$this->insert(__DIR__.'/profil/igelitka.php', $igelitka); ?>
                    <?php include 'profil/harmonogram.php' ?>
                </div>
                <br/>
            </div>
        </div>

    </div>