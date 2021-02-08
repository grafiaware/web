<?php
use Pes\Text\Text;
use Pes\Text\Html;

?>

            <div class="column middle aligned padding-vertical">
                <p><img <?= Html::attributes($imgKuponuAttributes)?> /></p>
                <p><a <?= Html::attributes($odkazKeStazeniAttributes)?>> <?= $text ?></a></p>
            </div>