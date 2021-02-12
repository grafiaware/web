<?php
use Pes\Text\Text;
use Pes\Text\Html;
?>
        <div class="row equal width padding-vertical">
            <div class="five wide column middle aligned">
                <a <?= Html::attributes($odkazBodyAttributes) ?>>
                    <img <?= Html::attributes($imagesBodyAttributes) ?> />
                </a>
            </div>
            <div class="eleven wide column">
                <?= Html::p(Text::mono($textFirmy), ["class"=>""]) ?>
            </div>
        </div>