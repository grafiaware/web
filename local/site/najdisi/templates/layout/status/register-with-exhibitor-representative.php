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
                    <input type="text" name="<?=ConfigurationCache::auth()['fieldNameJmeno']?>" placeholder="Jméno" required>
                </div>
                <div class="field">
                    <label>Heslo</label>
                    <input type="password" name="<?=ConfigurationCache::auth()['fieldNameHeslo']?>" placeholder="Heslo"
                           pattern="<?=ConfigurationCache::auth()['passwordPattern']?>"
                           title ="<?=ConfigurationCache::auth()['passwordInfo']?>"
                           required >
                </div>
                <div class="field">
                    <label>E-mail</label>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="field">
                    <div class="ui checkbox exhibitor">
                            <label>Žádám o registraci jako zástupce firmy</label>
                            <input type="checkbox" 
                                 name="fieldNameExhibitor"
                                 value="1">
                    </div>
                </div>
                <div class="ui input input-company">
                    <div class="field">
                        <label>Název firmy</label>
                        <input type="text" name="info" placeholder="Název společnosti" maxlength="50">
                        <p class="maly text">Vyplňte jméno firmy, kterou zastupujete. <br/> Pokud zastupujete více firem, oddělte je čárkou.</p>
                    </div>
                </div>
                
                <div class="ui fluid large buttons">
                    <a class="ui hide button" href="javascript:void(0)">Zavřít</a>
                    <button class="ui positive button" type="submit" name="register" value=1 >Registrovat</button>
                </div>
            </form>
        </div>
    </div>