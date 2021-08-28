<?php
use Site\Configuration;
?>
    <form class="ui inverted form centered" method="POST" action="auth/v1/login">
        <div class="ui icon left pointing dropdown button loginEnterKey">
            <i class="user icon"></i>
            <div class="menu">

                <div class="item header"><p><i class="user icon"></i>Přihlásit se</p></div>
                <div class="ui input">
                    <input type="text" name="<?=Configuration::loginLogoutController()['fieldNameJmeno']?>" placeholder="Jméno" required>
                </div>
                <div class="ui input">
                    <input class="notRequired" type="password" name="<?=Configuration::loginLogoutController()['fieldNameHeslo']?>" placeholder="Heslo" required>
                </div>
                <button class="ui positive button" type="submit" name="login" value=1>Přihlásit</button>
                <button class="ui fluid tertiary button" type="submit" name="forgottenpassword" value=1 formaction="auth/v1/forgottenpassword">Zapomněl jsem<br/>heslo</button>
          </div>
        </div>
    </form>
