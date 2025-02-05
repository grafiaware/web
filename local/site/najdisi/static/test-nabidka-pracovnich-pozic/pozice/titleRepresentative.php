                <div class="sixteen wide column">
                    <p class="podnadpis">
                        <i class="small level down alternate flipped icon"></i>        
                    <?php
                    if ($visitorJobRequestCount) {
                    ?>
                    <span class="ui big orange label">Hlásí se zájemci na pozici. Počet: <?= $visitorJobRequestCount ?? '0'?></span>
                    <?php
                    } else {
                    ?>
                    <span class="ui big grey label">Na pozici se dosud nikdo nehlásil.</span>
                    <?php
                    }
                    ?>
                    </p>
                </div>
                    