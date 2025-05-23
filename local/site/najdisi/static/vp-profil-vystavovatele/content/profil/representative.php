<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */

        $readonly = 'readonly="1"';
        $disabled = 'disabled="1"';
?>


            <div class="active title">
                <i class="dropdown icon"></i>
                Přihlášený zástupce vystavovatele
            </div>
            <div class="active content">
                <form class="ui huge form" action="" method="">
                    <!--   <div class="five wide field">
                           <div style="background-color: peachpuff; width: 200px; height: 200px; margin: 0 auto;">Foto</div>
                           </div>-->
                    <div class="two fields">
                        <div class="field">
                            <label>Přihlašovací jméno</label>
                            <input <?= $readonly ?> type="text" name="logNameRepresentative" placeholder="" maxlength="90" value="<?= $logNameRepresentative ?>">
                        </div>
                        <div class="field">
                            <label>Zastupuje</label>
                            <input <?= $readonly ?> type="text" name="nameCompany" placeholder="" maxlength="90" value="<?= $nameCompany ?>">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>E-mail</label>
                            <input <?= $readonly ?> type="email" name="email" placeholder="" maxlength="90" value="<?= $regmail ?>">
                        </div>
                    </div>
                    <div class="two fields">
                        <?php
                        if($readonly === '') {
                        ?>
                        <div class="field margin">
                            <button class="ui primary button" type="submit">Uložit</button>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                </form>
            </div>

