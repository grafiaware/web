        <div class="" data-template="<?= "default" ?>">
            <section>
                    <?= $headline ?>
                    <?= $perex ?>
            </section>
            <div class="ui grid">
                <?= $this->repeat(PROJECT_PATH."local/site/common/templates/paper-content/default/template.php", $contents, 'paperContent'); ?>
            </div>
        </div>