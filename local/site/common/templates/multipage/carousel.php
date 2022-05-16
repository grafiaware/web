    <div class="" data-template="<?= "carousel" ?>">
        <div class="carousel">
            <i class="huge angle left icon"></i>
            <div class="carousel-wrap">
                <?= $this->repeat(PROJECT_PATH."local/site/common/templates/paper/carousel-item.php", $pages, 'pag'); ?>
            </div>
            <i class="huge angle right icon"></i> 
        </div>
    </div>