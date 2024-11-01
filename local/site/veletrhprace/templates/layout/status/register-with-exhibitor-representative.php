<?php
use Site\ConfigurationCache;
?>
<form class="ui inverted form centered" method="POST" action="auth/v1/register">
        <div class="ui icon left pointing dropdown button">
            <i class="address card icon"></i>
            <div class="menu">
<!--                <div class="item header"><p><i class="user icon"></i>Registrace se <br/> připravuje</p></div>
                <p style="text-align: center;" class="item ui small button">Zavřít</p>-->
            <div class="item header"><p><i class="user icon"></i>Registrovat se</p></div>

                <div class="ui input">
                    <input type="text" name="<?=ConfigurationCache::auth()['fieldNameJmeno']?>" placeholder="Jméno" required>
                </div>
                <div class="ui input">
                    <input type="password" name="<?=ConfigurationCache::auth()['fieldNameHeslo']?>" placeholder="Heslo"
                           pattern="<?=ConfigurationCache::auth()['passwordPattern']?>"
                           title ="<?=ConfigurationCache::auth()['passwordInfo']?>"
                           required >
                </div>
                <div class="ui input">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
            
                <div class="">
                    <div class="ui checkbox exhibitor">
                      <input type="checkbox"
                                 name="fieldNameExhibitor"
                                 value="1">
                      <label>Zastupuji vystavovatele</label>
                    </div>
                    <div class="ui input input-company">
                        <input class="" type="text" name="info" placeholder="Název společnosti" maxlength="50">
                        <p class="maly text">Vyplňte jméno společnosti, kterou zastupujete. <br/> Pokud zastupujete více společností, oddělte je čárkou.</p>
                    </div>
                </div>


                <button class="ui positive button" type="submit" name="register" value=1 >Registrovat</button>
            </div>
        </div>
    </form>
