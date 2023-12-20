<?php
use Site\ConfigurationCache;
?>
    <button class="ui page icon button btn-register">
            <i class="address card icon"></i>
    </button>

    <div class="ui mini page modal transition hidden register">
        <i class="white close icon"></i>
        <div class="content">
            <p class="text velky">Registrovat se</p>
            <form class="ui form centered" method="POST" action="auth/v1/register">
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
                <div class="field">
                    <div class="ui checkbox exhibitor">
                            <label>Zastupuji vystavovatele</label>
                            <input type="checkbox" 
                                 name="fieldNameExhibitor"
                                 value="1">
                    </div>
                </div>
                <div class="ui input input-company">
                    <div class="field">
                        <label>Název společnosti</label>
                        <input type="text" name="info" placeholder="Název společnosti" maxlength="50">
                        <p class="maly text">Vyplňte jméno společnosti, kterou zastupujete. <br/> Pokud zastupujete více společností, oddělte je čárkou.</p>
                    </div>
                </div>
                
                <div class="ui fluid large buttons">
                    <a class="ui hide button" href="javascript:void(0)">Zavřít</a>
                    <button class="ui positive button" type="submit" name="register" value=1 >Registrovat</button>
                </div>
            </form>
        </div>
    </div>


<!--<form class="ui inverted form centered" method="POST" action="auth/v1/register">
        <div class="ui icon left pointing dropdown button">
            <i class="address card icon"></i>
            <div class="menu">
<!--                <div class="item header"><p><i class="user icon"></i>Registrace se <br/> připravuje</p></div>
                <p style="text-align: center;" class="item ui small button">Zavřít</p>
            <div class="item header"><p><i class="user icon"></i>Registrovat se</p></div>

                <div class="ui input">
                    <input type="text" name="<?php /*=ConfigurationCache::loginLogoutController()['fieldNameJmeno']*/?>" placeholder="Jméno" required>
                </div>
                <div class="ui input">
                    <input type="password" name="<?php /*=ConfigurationCache::loginLogoutController()['fieldNameHeslo']*/?>" placeholder="Heslo"
                           pattern="<?php /*=ConfigurationCache::loginLogoutController()['passwordPattern']*/?>"
                           title ="<?php /*=ConfigurationCache::loginLogoutController()['passwordInfo']*/?>"
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
    </form>-->