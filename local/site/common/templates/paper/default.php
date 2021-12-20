        <div class="" data-template="<?= "default" ?>">
            <div class="ui grid">
                <div class="sixteen wide column">
                    <section>
                            <?= $headline ?>
                            <?= $perex ?>
                    </section>
                </div>
            </div>
            <div class="ui grid">
                <?= $this->repeat(PROJECT_PATH."local/site/common/templates/paper-content/default.php", $contents, 'paperContent'); ?>
            </div>
        </div>