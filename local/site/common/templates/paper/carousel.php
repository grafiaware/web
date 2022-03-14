    <div class="" data-template="<?= "carousel-item" ?>">
        <div class="ui grid">
            <div class="sixteen wide column">
                <section class="carousel-info">
                        <?= $headline ?>
                        <?= $perex ?> 
                </section>
                <?= $this->repeat(PROJECT_PATH."local/site/common/templates/paper-content/carousel.php", $sections, 'paperSection'); ?>
            </div>
        </div>
    </div>
    