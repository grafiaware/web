
        <div class="row equal width padding-vertical">
            <div class="five wide column middle aligned">
                <a <?= $this->attributes($odkazBodyAttributes) ?>>
                    <img <?= $this->attributes($imagesBodyAttributes) ?> />
                </a>
            </div>
            <div class="eleven wide column">
                <?=  $this->p($this->mono($textFirmy), ["class"=>""]) ?>
            </div>
        </div>