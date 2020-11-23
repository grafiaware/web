
        <div class="stretched row">
            <div class="eight wide column">
                <p><b><?= $jmeno ?></b></p>
                <p><?= $this->mono($funkce)?></p>
            </div>
            <div class="eight wide column">
                <p><b><?= $this->mono($nazevPrednasky)?></b></p>
                <p><?= $datumCas ?></p>
                <p><a <?= $this->attributes($odkazPrednaskyAttributes) ?>> <?= $odkazPrednaskyText ?> </a></p>
            </div>
        </div>

        

