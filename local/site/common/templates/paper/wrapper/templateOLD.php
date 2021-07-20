<div data-component="presented" data-template="<?= $paperAggregate->getTemplate() ?>" class="ui segment mceNonEditable">
    <div class="grafia segment headlined editable">
        <article class="" >
            <section>
                <headline class="ui header">
                    <?= $headline ?>
                </headline>
                <perex>
                    <?= $perex ?>
                </perex>
            </section>
            <content>
                <?=
        $this->repeat(PROJECT_PATH."public/web/templates/paper-content/default/template.php", $contents, 'paperContent'); ?>
            </content>
        </article>
    </div>
</div>

