<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

use Site\Configuration;

use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

?>
    <div class="profil">
        <div class="ui stackable centered grid">
            <div class="column">
                <div class="ui styled fluid accordion">
                    <?= $this->insert(__DIR__.'/profil/presenter.php', $presenterItem) ?>
                </div>
                <br/>
            </div>
        </div>
        <?php include Configuration::componentControler()['templates']."presenter-job/content/vypis-pozic.php"; ?>

        <p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll">Náš program</p>
        <?php include 'profil/harmonogram.php' ?>

    </div>