<template data-templatepath="<?= $templatePath ?>" class="editable">
     <div class="ui segment mceNonEditable">
        <article class="paper">
            <headline class="ui header">
                <?= $headline ?>
            </headline>
            <perex>
                <?= $perex ?>
            </perex>
            <?= $this->repeat("public/web/templates/paper_content/$name/template.php", $contents) ?>
        </article>
    </div>
</template>

