        <div class="" data-template="<?= "default" ?>">
            <div class="ui grid">
                <div class="sixteen wide column">
                    <?= $headline ?>
                </div>
            </div>
            <div class="ui grid vertikalne-prohodit">
                <div class="sixteen wide column">
                    <?= $perex ?>
                </div>
                <?= $this->repeat(PROJECT_PATH."local/site/common/templates/red/paper/section/default.php", $sections, 'paperSection'); ?>
            </div>
        </div>