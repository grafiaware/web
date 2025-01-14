<?php
use Site\ConfigurationCache;
?>

    <!--prihlaseni je modalni okno, jde zavrit pouze pri stisku buttonu 'zavrit'-->
    <button class="ui page icon button btn-login">
        <i class="user icon"></i>
    </button>

    <div class="ui mini page modal transition hidden login">
        <i class="white close icon"></i>
        <div class="content">
            <p class="text velky">Přihlásit se</p>
            <form class="ui form centered" method="POST" action="auth/v1/login">
                <div class="field">
                    <label>Přihlašovací jméno</label>
                    <input type="text" name="<?=ConfigurationCache::auth()['fieldNameJmeno']?>" placeholder="Jméno" required>
                </div>
                <div class="field">
                    <label>Heslo</label>
                    <input type="password" name="<?=ConfigurationCache::auth()['fieldNameHeslo']?>" placeholder="Heslo" required>
                </div>
                <div class="ui fluid large buttons">
                    <a class="ui hide button" href="javascript:void(0)">Zavřít</a>
                    <button class="ui positive button" tabindex="1" type="submit" name="login" value=1>Přihlásit</button>
                </div>
                <p class="text">
                    <button class="ui basic fluid button password-btn" tabindex="0" type="submit" name="forgottenpassword" value=1 formaction="auth/v1/forgottenpassword">Zapomněla/zapomněl jsem heslo</button>
                </p>
            </form>
        </div>
    </div>


