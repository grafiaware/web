    <div class="" data-template="<?= "carousel" ?>">
        <div class="carouselPaper">
            <div class="carousel-wrap">
                <div class="" data-template="<?= "carouselPaper-item" ?>">
                    <div class="ui grid">
                        <div class="sixteen wide column">
                            <section class="carouselPaper-info">
                                    <?= $headline ?>
                                    <?= $perex ?> 
                            </section>
                            <i class="huge angle left icon"></i>
                            <div class="carousel-cards">
                                <?= $this->repeat(__DIR__."/section/carousel-item.php", $sections, 'paperSection'); ?>
                            </div>
                            <i class="huge angle right icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    