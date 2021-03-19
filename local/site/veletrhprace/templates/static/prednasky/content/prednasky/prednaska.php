<?php
use Pes\Text\Text;
use Pes\Text\Html;

if ($publikovano) {
?>
        <div class="stretched row">
            
            <div class="eight wide column">
                <p><b><?= Text::mono($nazevPrednasky)?></b></p>
                <p><?= "$casOd - $casDo" ?></p>
                <!--<p><a <?= Html::attributes($odkazPrednaskyAttributes) ?>> <?= $odkazPrednaskyText ?> </a></p>-->
            </div>
            <div class="eight wide column">
                <p><b><?= $jmeno ?></b></p>
                <p><?= Text::mono($funkce)?></p>
                <p><?= Text::mono($perex)?></p>
            </div>
        </div>
<?php 
}


