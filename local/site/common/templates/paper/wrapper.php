<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Component\Renderer\Html\Authored\Paper\ElementWrapper;
use Component\Renderer\Html\Authored\Paper\PaperButtonsRenderer;
use Red\Model\Entity\PaperAggregatePaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var ElementWrapper $elementWrapper */
/** @var PaperButtonsRenderer $buttons */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

?>
    <?= isset($buttons) ? $buttons->renderPaperTemplateButtonsForm($paperAggregate) : "" ?>
    <?= isset($buttons) ? $buttons->renderPaperButtonsForm($paperAggregate) : "" ?>
        <article class="" data-template="<?=$paperAggregate->getTemplate()?>">
            <section>
                    <?= $elementWrapper->wrapHeadline($paperAggregate) ?>
                    <?= $elementWrapper->wrapPerex($paperAggregate) ?>
            </section>
            <section>
                <?=
        $this->repeat(PROJECT_PATH."local/site/common/templates/paper-content/default.php", $paperAggregate->getPaperContentsArraySorted(PaperAggregatePaperContentInterface::BY_PRIORITY), 'paperSection'); ?>
            </section>
        </article>