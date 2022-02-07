        <div class="" data-template="<?= "default" ?>">
            <div class="ui grid">
                <?= $headline ?>
            </div>
            <div class="ui grid vertikalne-prohodit">
                <div class="sixteen wide column">
                    <?= $perex ?>
                </div>
                <?= $this->repeat(PROJECT_PATH."local/site/common/templates/paper-content/default.php", $sections, 'paperSection'); ?>
            </div>
        </div>