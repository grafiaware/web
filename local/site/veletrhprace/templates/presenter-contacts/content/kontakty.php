<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

?>
<div class="navazat-kontakt">
    <div class="ui grid">
        <div class="sixteen wide column center aligned">
            <?= $this->insert(__DIR__.'/kontakty/profilove-udaje.php') ?>
        </div>
        <div class="sixteen wide column center aligned">
            <div class="ui large button profil-visible">Přeji si vyplnit své osobní údaje</div>
        </div>
        <div class="sixteen wide column">
            <div class="profil hidden">
                <?= $this->insert(__DIR__.'/kontakty/osobni-udaje.php') ?>
            </div>
        </div>
        <div class="sixteen wide column">
            <content>
                <div class="kontaktni-udaje-firmy">
                    <?= $this->repeat(__DIR__.'/kontakty/firemni-udaje.php', $corporateData) ?>
                </div>
            </content>
        </div>
    </div>
</div>