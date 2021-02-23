<?php
use Site\Configuration;
?>
<form class="ui inverted form centered" method="POST" action="auth/v1/register">
        <div class="ui icon top left pointing dropdown button loginEnterKey">
            <i class="user icon"></i>
            <div class="menu">
                <div class="item header"><p><i class="user icon"></i>Registrovat se</p></div>
                <div class="ui input">
                    <input type="text" name="<?=Configuration::loginLogoutControler()['fieldNameJmeno']?>" placeholder="Jméno" required>
                </div>
                <div class="ui input">
                    <input type="password" name="<?=Configuration::loginLogoutControler()['fieldNameHeslo']?>" placeholder="Heslo"
                           pattern="<?=Configuration::loginLogoutControler()['emailPattern']?>"
                           title ="<?=Configuration::loginLogoutControler()['emailInfo']?>"
                           required >
                </div>
                <div class="ui input">
                    <input type="email" name="<?=Configuration::loginLogoutControler()['fieldNameEmail']?>" placeholder="Email" required>
                </div>
                <button class="ui positive button" type="submit" name="register" value=1 >Registrovat</button>
            </div>
        </div>
    </form>
