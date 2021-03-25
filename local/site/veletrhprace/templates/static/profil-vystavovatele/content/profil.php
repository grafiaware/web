<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

?>
    <div class="profil">
        <div class="ui stackable centered grid">
            <div class="column">
                <div class="ui styled fluid accordion">
                    <?= $this->insert(__DIR__.'/profil/osobni-udaje.php', $personalData) ?>
                </div>
                <br/>
            </div>
        </div>
        <p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll">Náš program</p>
        <?= $this->insert(__DIR__.'/profil/harmonogram.php', $timeline) ?>
    </div>