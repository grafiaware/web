<?php
use Site\ConfigurationCache;
?>
<!--<form class="ui inverted form centered" method="POST" action="auth/v1/register">
        <div class="ui icon left pointing dropdown button">
            <i class="address card icon"></i>
            <div class="menu">
                <div class="item header"><p><i class="user icon"></i>Registrace se <br/> připravuje</p></div>
                <p style="text-align: center;" class="item ui small button">Zavřít</p>
            <div class="item header"><p><i class="user icon"></i>Registrovat se</p></div>

                <div class="ui input">
                    <input type="text" name="<?=ConfigurationCache::loginLogoutController()['fieldNameJmeno']?>" placeholder="Jméno" required>
                </div>
                <div class="ui input">
                    <input type="password" name="<?=ConfigurationCache::loginLogoutController()['fieldNameHeslo']?>" placeholder="Heslo"
                           pattern="<?=ConfigurationCache::loginLogoutController()['passwordPattern']?>"
                           title ="<?=ConfigurationCache::loginLogoutController()['passwordInfo']?>"
                           required >
                </div>
                <div class="ui input">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <button class="ui positive button" type="submit" name="register" value=1 >Registrovat</button>
            </div>
        </div>
    </form>-->

<button class="ui page icon button btn-register">
        <i class="address card icon"></i>
</button>

    <div class="ui mini page modal transition hidden register">
        <i class="white close icon"></i>
        <div class="content">
            <h2>Registrovat se</h2>
            <form class="ui form centered" method="POST" action="auth/v1/login">
                <div class="field">
                    <label>Přihlašovací jméno</label>
                    <input type="text" name="<?=ConfigurationCache::loginLogoutController()['fieldNameJmeno']?>" placeholder="Jméno" required>
                </div>
                <div class="field">
                    <label>Heslo</label>
                     <input type="password" name="<?=ConfigurationCache::loginLogoutController()['fieldNameHeslo']?>" placeholder="Heslo"
                           pattern="<?=ConfigurationCache::loginLogoutController()['passwordPattern']?>"
                           title ="<?=ConfigurationCache::loginLogoutController()['passwordInfo']?>"
                           required >
                </div>
                <div class="field">
                    <label>E-mail</label>
                     <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="ui fluid large buttons">
                    <a class="ui hide button" href="javascript:void(0)">Zavřít</a>
                    <button class="ui positive button" type="submit" name="register" value=1 >Registrovat</button>
                </div>
            </form>
        </div>
    </div>
