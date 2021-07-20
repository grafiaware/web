<?php
use Red\Model\Entity\PaperAggregatePaperContentInterface;
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>
<div data-component="presented" data-template="<?= $paperAggregate->getTemplate() ?>" class="ui segment mceNonEditable">
    <div class="grafia segment headlined editable">
        <article class="" >
            <section>
                <headline class="ui header">
                    <?= $paperAggregate->getHeadline() ?>
                </headline>
                <perex>
                    <p><?= $paperAggregate->getPerex() ?></p>
                </perex>
            </section>
            <content>
                <?= $this->repeat(PROJECT_PATH."public/web/templates/paper-content/default/template.php", $paperAggregate->getPaperContentsArray(), 'paperContent'); ?>
            </content>
        </article>
    </div>
</div>