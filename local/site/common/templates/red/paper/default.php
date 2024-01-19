    <!--<div class="notClickable">-->
    <div class="mceNonEditable">
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
                <?= $this->repeat(__DIR__."/section/default.php", $sections, 'paperSection'); ?>
            </div>
        </div>
    </div>    