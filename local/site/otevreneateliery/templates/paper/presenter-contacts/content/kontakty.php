<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

?>
<div class="navazat-kontakt">
    <div class="ui grid">
        <div class="sixteen wide column">
            <content>
                <div class="kontaktni-udaje-firmy">
                    <div class="ui grid stackable centered">
                        <p class="text velky tucne nastred">Firemní údaje</p>
                        <?= $this->repeat(__DIR__.'/kontakty/firemni-udaje.php', $corporateContacts) ?>
                        <?= $this->insert(__DIR__.'/kontakty/firemni-pobocka.php', $corporateAddress) ?>
                     </div>
                </div>
            </content>
        </div>
    </div>
</div>
