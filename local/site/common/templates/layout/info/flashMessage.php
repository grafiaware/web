<?php
use Pes\Text\Text;
?>

<!--                <div class="poznamky flash">
                    <div class="ui fluid pointing below red basic label"><i class="large yellow bolt icon"></i></div>
                    <div class="content">
                        <p>
                        <?= Text::nl2br($flashMessage ?? '') ?>
                        </p>
                        <p>
                        <?= $postCommand ?? '' ?>
                        </p>
                    </div>
                </div>-->

            <div class="ui small blue toast" id="<?= (($flashMessage ?? '') ? "domtoast" : "") ?>">
                <div class="content">
                    <p><i class="circle times icon"></i>Notice</p>
                    <p>
                        <?= Text::nl2br($flashMessage ?? '') ?>
                    </p>
                    <p>
                        <?= $postCommand ?? '' ?>
                    </p>
                </div>
            </div>
