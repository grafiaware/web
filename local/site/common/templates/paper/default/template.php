<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Component\Renderer\Html\Authored\ElementWrapper;
use Component\Renderer\Html\Authored\Buttons;
use Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var ElementWrapper $elementWrapper */
/** @var Buttons $buttons */
/** @var PaperAggregateInterface $paperAggregate */

?>
    <?= isset($buttons) ? $buttons->getPaperTemplateButtonsForm($paperAggregate) : "" ?>
    <?= isset($buttons) ? $buttons->getPaperButtonsForm($paperAggregate) : "" ?>
        <article class="" data-template="<?=$paperAggregate->getTemplate()?>">
            <section>
                    <?= $elementWrapper->wrapHeadline($paperAggregate) ?>
                    <?= $elementWrapper->wrapPerex($paperAggregate) ?>
            </section>
            <section>
                <?=
        $this->repeat(PROJECT_PATH."local/site/common/templates/paper-content/default/template.php", $paperAggregate->getPaperContentsArraySorted(PaperAggregateInterface::BY_PRIORITY), 'paperContent'); ?>
            </section>
        </article>