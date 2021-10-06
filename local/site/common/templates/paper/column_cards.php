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

    <div class="" data-template="<?= "column_cards" ?>">
        <section class="">
            <?= $headline ?>
            <?= $perex ?>
        </section>
        <div class="ui three column grid stackable centered">
            <?= $this->repeat(PROJECT_PATH."local/site/common/templates/paper-content/column_cards/template.php", $contents, 'paperContent'); ?>
        </div>
    </div>