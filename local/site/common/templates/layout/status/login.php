<?php
use Site\ConfigurationCache;
?>

<!--semanticky dropdown-->
<!--    <form class="ui inverted form centered" method="POST" action="auth/v1/login">
        <div class="ui icon left pointing dropdown button loginEnterKey">
            <i class="user icon"></i>
            <div class="menu">

                <div class="item header"><p><i class="user icon"></i>Přihlásit se</p></div>
                <div class="ui input">
                    <input type="text" name="<?=ConfigurationCache::loginLogoutController()['fieldNameJmeno']?>" placeholder="Jméno" required>
                </div>
                <div class="ui input">
                    <input class="notRequired" type="password" name="<?=ConfigurationCache::loginLogoutController()['fieldNameHeslo']?>" placeholder="Heslo" required>
                </div>
                <button class="ui positive button" type="submit" name="login" value=1>Přihlásit</button>
                <button class="ui fluid tertiary button" type="submit" name="forgottenpassword" value=1 formaction="auth/v1/forgottenpassword">Zapomněl jsem<br/>heslo</button>
          </div>
        </div>
    </form>-->

<!--prihlaseni se zobrazi jako dropdown, ale zavre se pouze pri kliknuti na ikonu prihlaseni-->
<!--    <form class="ui inverted form centered " method="POST" action="auth/v1/login">
            <button class="ui icon button btn-login"><i class="user icon"></i></button>
            <div class="menu-login ">
                <div class="item header"><p><i class="user icon"></i>Přihlásit se</p></div>
                <div class="ui input">
                    <input class="loginEnterKey" type="text" name="<?=ConfigurationCache::loginLogoutController()['fieldNameJmeno']?>" placeholder="Jméno" required>
                </div>
                <div class="ui input">
                    <input class="notRequired" type="password" name="<?=ConfigurationCache::loginLogoutController()['fieldNameHeslo']?>" placeholder="Heslo" required>
                </div>
                <button class="ui positive button" type="submit" name="login" value=1>Přihlásit</button>
                <button class="ui fluid tertiary button" type="submit" name="forgottenpassword" value=1 formaction="auth/v1/forgottenpassword">Zapomněl jsem<br/>heslo</button>
           </div>
    </form>-->

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
                    <input type="text" name="<?=ConfigurationCache::loginLogoutController()['fieldNameJmeno']?>" placeholder="Jméno" required>
                </div>
                <div class="field">
                    <label>Heslo</label>
                    <input class="notRequired" type="password" name="<?=ConfigurationCache::loginLogoutController()['fieldNameHeslo']?>" placeholder="Heslo" required>
                </div>
                <div class="ui fluid large buttons">
                    <a class="ui hide button" href="javascript:void(0)">Zavřít</a>
                    <button class="ui positive button" type="submit" name="login" value=1>Přihlásit</button>
                </div>
                <p><button class="ui tertiary button" type="submit" name="forgottenpassword" value=1 formaction="auth/v1/forgottenpassword">Zapomněl jsem heslo</button></p>
            </form>
        </div>
    </div>

