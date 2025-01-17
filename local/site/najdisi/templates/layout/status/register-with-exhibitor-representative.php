<?php
use Site\ConfigurationCache;
?>
    <button class="ui page icon button btn-register">
            <i class="address card icon"></i>
    </button>

    <div class="ui tiny page modal transition hidden register">
        <i class="white close icon"></i>
        <div class="content">
            <p class="text velky">Registrovat se</p>
            <div class="register-info">
                <div class="ui two column grid">
                    <div class="two wide column center aligned">
                        <button class="ui circular orange basic icon button register-info-button"><i class="info icon"></i></button>
                    </div>
                    <div class="thirteen wide column">
                        <div class="register-info-text">
                            <i class="close icon"></i>
                            <p class="text seznam">Přihlašovací jméno, heslo a email jsou povinné údaje.</p>
                            <p class="text seznam">Po jejich zadání a potvrzení se stanete registrovaným návštěvníkem.</p>
                            <div class="ui divider"></div>
                            <p class="text seznam">POKUD CHCETE ZASTUPOVAT FIRMU:</p>
                            <p class="text seznam">Jako zástupce firmy zaškrtněte políčko dole ve formuláři.</p>
                            <p><?=ConfigurationCache::auth()['registrationInfo'] ?? ''?></p>
                        </div>
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
                        <input class="input-company" type="text" name="info" placeholder="Název společnosti" maxlength="50">
                        <p class="maly text">Vyplňte jméno firmy, kterou zastupujete. <br/> Pokud zastupujete více firem, oddělte je čárkou.</p>
                    </div>
                </div>
                <div class="register-info-representative">
                    <p class="text seznam">Info pro reprezentanta</p>
                    <p class="text seznam">Zástupcem firmy se stanete až po schválení organizátorem. O schválení budete informováni emailem a pak budete moci upravovat data firmy.</p>
                    <div><p class="ui orange button">Rozumím</p></div>
                </div>
                
                <div class="ui fluid large buttons">
                    <a class="ui hide button" href="javascript:void(0)">Zavřít</a>
                    <button class="ui positive button" type="submit" name="register" value=1 >Registrovat</button>
                </div>
            </form>
        </div>
    </div>