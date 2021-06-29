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

    <?= isset($buttons) ? $buttons->renderPaperTemplateButtonsForm($paperAggregate) : "" ?>
    <?= isset($buttons) ? $buttons->renderPaperButtonsForm($paperAggregate) : "" ?>
<div data-component="presented" data-template="<?= $paperAggregate->getTemplate() ?>" class="">
    <form>
        <article class="edit-html borderDance">
            
        </article>
    </form>
</div>