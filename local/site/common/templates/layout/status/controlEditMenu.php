<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>  
            <div class="button-edit-menu">
                <form class="ui form" method="POST" action="">
                    <button class="<?= empty($editMenu) ? "ui small teal icon button": "ui small teal basic icon button" ?>"
                            data-tooltip="<?= empty($editMenu) ? "Zapnout editaci menu" : "Vypnout editaci menu" ?>" 
                            data-position="left center"
                            type="submit" 
                            name="edit_menu" 
                            value="<?= empty($editMenu) ? 1 : 0 ?>" 
                            formtarget="_self"
                            formaction="red/v1/presentation/edit_menu">
                        <i class="pencil alternate icon"></i>
                    </button>
                </form>
            </div>
