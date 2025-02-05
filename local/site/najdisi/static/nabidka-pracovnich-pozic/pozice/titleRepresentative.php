                <div class="sixteen wide column">
                    <div class="ui message">
                        <p>
                            <span class="ui small red text">V přehledu pozic se zobrazují jen publikované pracovní pozice. Prohlédnout si pozice, jejichž zveřejnění jste již zrušili můžete v profilu firmy.</span>
                        </p>       
                    </div>                 
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
                    