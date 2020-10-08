<?php
use Model\Entity\PaperAggregateInterface;
/** @var PaperAggregateInterface $paperAggregate */
?>
<div data-component="presented" data-template="<?= $paperTemplateName ?>" class="ui segment mceNonEditable">
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
                <?= $this->repeat(PROJECT_PATH."templates/paper_content/default/template.php", $paperAggregate->getPaperContentsArray()); ?>
            </content>
        </article>
    </div>
</div>