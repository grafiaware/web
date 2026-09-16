<?php
use Pes\Core\Text\Text;
use Pes\Core\Text\Html;
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