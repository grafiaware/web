<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
                <form class="ui form" method="POST" action="">
                    <button class="fluid ui labeled icon large button" type="submit" name="edit_menu" value="<?= empty($editMenu) ? 1 : 0 ?>" formtarget="_self"
                            formaction="red/v1/presentation/edit_menu">
                        <i class="edit icon"></i>
                        <?= empty($editMenu) ? "Zapnout editaci menu" : "Vypnout editaci menu" ?>
                    </button>
                </form>
