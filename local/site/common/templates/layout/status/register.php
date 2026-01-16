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
            <div class="register-info">
                <div class="ui two column grid">
                    <div class="thirteen wide column">
                        <div class="register-info-text">
                            <i class="close icon"></i>
                            <p class="text seznam">Přihlašovací jméno, heslo a email jsou povinné údaje.</p>
                            <p class="text seznam">Po jejich zadání a potvrzení se stanete registrovaným návštěvníkem.</p>
                            <div class="ui divider"></div>
                            <p><?=ConfigurationCache::auth()['registrationInfo'] ?? ''?></p>
                        </div>
                    </div>
                    <div class="two wide column center aligned">
                        <button class="ui circular orange basic icon button register-info-button priority"><i class="info icon"></i></button>
                    </div>
                </div>
            </div>            
            <form class="ui form centered" method="POST" action="auth/v1/register">
                <div class="field">
                    <label>Přihlašovací jméno</label>
                     <input type="text" name="<?=ConfigurationCache::auth()['fieldNameJmeno']?>" placeholder="Jméno"
                           pattern="<?=ConfigurationCache::auth()['jmenoPattern']?>"
                           title ="<?=ConfigurationCache::auth()['jmenoInfo']?>"
                           required >                    
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
                     <input type="email" name="email" placeholder="email" required>
                </div>
                <div class="ui fluid large buttons">
                    <a class="ui hide button" href="javascript:void(0)">Zavřít</a>
                    <button class="ui positive button" type="submit" name="register" value=1 >Registrovat</button>
                </div>
            </form>
        </div>
    </div>
