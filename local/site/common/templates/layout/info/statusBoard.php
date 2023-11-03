                <div class="ui tiny red basic icon button btn-poznamky"><i class="large clipboard outline icon"></i></div>
                <div class="poznamky" style="display: none;">
                    <div class="ui fluid pointing below red basic label"><i class="large clipboard outline icon"></i></div>
                    <div class="content">
                        <?php 
                        foreach ($infos as $infoPrettyPrint) {
                            echo $infoPrettyPrint ?? '';
                        }
                        ?>
                    </div>
                </div>

