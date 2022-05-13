<?php
use Red\Middleware\Redactor\Controler\UserActionControler;
?>
            <div class="button-edit-menu">
                <form class="ui form" method="POST" action="">
                    <button class="<?= empty($editMenu) ? "ui small teal icon button": "ui small teal basic icon button" ?>"
                            data-tooltip="<?= empty($editMenu) ? "Zapnout editaci menu" : "Vypnout editaci menu" ?>"
                            data-position="left center"
                            type="submit"
                            name="<?= UserActionControler::FORM_USER_ACTION_EDIT_MENU ?>"
                            value="<?= empty($editMenu) ? 1 : 0 ?>"
                            formtarget="_self"
                            formaction="red/v1/presentation/edit_menu"
                            disabled>
                        <i class="pencil alternate icon"></i>
                    </button>
                </form>
            </div>
