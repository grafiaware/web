    <div class="" data-template="<?= "carousel" ?>">
        <div id="carousel">
            <i class="huge angle left icon"></i>
            <div class="carousel-wrap">
                <?= $this->repeat(PROJECT_PATH."local/site/common/templates/paper/carousel.php", $sections, 'paperSection'); ?>
            </div>
            <i class="huge angle right icon"></i>
        </div>
    </div>