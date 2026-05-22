<?php
use Pes\Core\Text\Text;
use Pes\Core\Text\Html;

?>

            <div class="column middle aligned padding-vertical">
                <p><img <?= Html::attributes($imgKuponuAttributes)?> /></p>
                <p><a <?= Html::attributes($odkazKeStazeniAttributes)?>> <?= $text ?></a></p>
            </div>