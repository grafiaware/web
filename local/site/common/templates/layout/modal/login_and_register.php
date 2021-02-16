<?php
use Site\Configuration;
?>
<form class="ui inverted form centered" method="POST" action="auth/v1/login">
        <div class="ui icon top left pointing dropdown button loginEnterKey">
            <i class="user icon"></i>
            <div class="menu">
                <div class="item header"><p><i class="user icon"></i>Registrovat se</p></div>
                <div class="ui input">
                    <input type="text" name="<?=Configuration::loginLogoutControler()['fieldNameJmeno']?>" placeholder="Jméno" required>
                </div>
                <div class="ui input">
                    <input type="password" name="<?=Configuration::loginLogoutControler()['fieldNameHeslo']?>" placeholder="Heslo" 
                           pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                           title = "Heslo musí obsahovat nejméně jedno velké písmeno, jedno malé písmeno a jednu číslici. Jiné znaky než písmena a číslice nejsou povoleny. Délka musí být nejméně 8 znaků."  
                           required > 
                </div>
                <div class="ui input">
                    <input type="email" name="<?=Configuration::loginLogoutControler()['fieldNameEmail']?>" placeholder="Email" required>
                </div>
                <button class="ui positive button" type="submit" name="login" value=1 formaction="auth/v1/register">Přihlásit</button>
            </div>              
        </div>
    </form>
