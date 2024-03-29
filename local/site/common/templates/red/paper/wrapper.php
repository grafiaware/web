<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Component\Renderer\Html\Content\Authored\Paper\ElementWrapper;
use Red\Component\Renderer\Html\Content\Authored\Paper\PaperButtonsRenderer;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var ElementWrapper $elementWrapper */
/** @var PaperButtonsRenderer $buttons */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */

?>
    <?= isset($buttons) ? $buttons->renderPaperTemplateButtonsForm($paperAggregate) : "" ?>
    <?= isset($buttons) ? $buttons->renderPaperButtonsForm($paperAggregate) : "" ?>
        <article class="" data-template="<?=$paperAggregate->getTemplate()?>">
            <section>
                    <?= $elementWrapper->wrapHeadline($paperAggregate) ?>
                    <?= $elementWrapper->wrapPerex($paperAggregate) ?>
            </section>
            <section>
                <?= $this->repeat(__DIR__."/section/default.php", $paperAggregate->getPaperSectionsArraySorted(PaperAggregatePaperSectionInterface::BY_PRIORITY), 'paperSection'); ?>
            </section>
        </article>