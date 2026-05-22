<?php
use Pes\Core\Text\Text;
use Pes\Core\Text\Html;

?>

            <div class="column middle aligned">
                <a <?= Html::attributes($odkazBodyAttributes) ?>> <?= Text::mono( $odkazText ) ?> </a>
            </div>