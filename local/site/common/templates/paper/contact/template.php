<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Component\Renderer\Html\Authored\Paper\ElementWrapper;
use Component\Renderer\Html\Authored\Paper\Buttons;
use Red\Model\Entity\PaperAggregatePaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var ElementWrapper $elementWrapper */
/** @var Buttons $buttons */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

?>

    <article class="" data-template="<?= "contact" ?>">
        <section class="">
            <?= $headline ?>
            <?= $perex ?>
        </section>
        <div class="ui three column grid stackable centered">
            <div class="column">
                <content id="content_4" class="">
                    <div class="ui segment">
                        <h3 class="ui header">Pozice 1</h3>
                        <div class="content">
                            <p><b>Ing. Valdemar Novák</b></p>
                            <p>Tel.: +420 377 543 345</p>
                            <p>Mobil: +420 774 484 850</p>
                            <p>E-mail: <a href="mailto:info@grafia.cz">info@grafia.cz</a></p>
                            <p>Fax: 378 771 211</p>
                            <p>Kancelář: Budilova 4, Plzeň</p>
                        </div>
                    </div>
                </content>
            </div>
            <?= $this->repeat(PROJECT_PATH."local/site/common/templates/paper-content/contact/template.php", $contents, 'paperContent'); ?>
        </div>
    </article>