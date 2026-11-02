<?php
use Site\ConfigurationCache;

/**
 * A1 (docs/vp/zadani/A1.md): registrace e-mailem, heslo volitelné, přihlašovací jméno se vytvoří z e-mailu.
 * Vkládá se z register-with-exhibitor-representative.php při zapnutém přepínači vp.registrace-emailem.
 */
$auth = ConfigurationCache::auth();
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
                    <div class="thirteen wide column">
                        <div class="register-info-text">
                            <i class="close icon"></i>
                            <p class="text seznam">Stačí e-mail. Pošleme Vám odkaz, kterým registraci dokončíte.</p>
                            <p class="text seznam">Heslo zadat nemusíte – přihlásíte se pak odkazem z e-mailu. Heslo si můžete nastavit kdykoli později.</p>
                            <div class="ui divider"></div>
                            <p class="text seznam">POKUD CHCETE ZASTUPOVAT FIRMU:</p>
                            <p class="text seznam">Jako zástupce firmy zaškrtněte políčko dole ve formuláři.</p>
                            <p><?= $auth['registrationInfo'] ?? '' ?></p>
                        </div>
                    </div>
                    <div class="two wide column center aligned">
                        <button class="ui circular orange icon button register-info-button priority"><i class="info icon"></i></button>
                    </div>
                </div>
            </div>
            <form class="ui form centered" method="POST" action="auth/v1/register">
                <div class="required field">
                    <label>E-mail</label>
                    <input type="email" name="email" placeholder="vas@email.cz" maxlength="100"
                           autocomplete="email" inputmode="email" autocapitalize="none" spellcheck="false" required>
                </div>
                <div class="field">
                    <label>Heslo (nepovinné)</label>
                    <input type="password" name="<?= $auth['fieldNameHeslo'] ?>" placeholder="Heslo"
                           pattern="<?= $auth['passwordPattern'] ?>"
                           title="<?= $auth['passwordInfo'] ?>"
                           autocomplete="new-password">
                    <p class="maly text"><?= $auth['passwordInfo'] ?></p>
                </div>
                <div class="field">
                    <div class="ui checkbox exhibitor">
                        <label>Žádám o registraci jako zástupce firmy</label>
                        <input type="checkbox" name="fieldNameExhibitor" value="1">
                    </div>
                </div>
                <div class="ui input input-company">
                    <div class="field">
                        <label>Název firmy</label>
                        <input class="input-company" type="text" name="info" placeholder="Název společnosti" maxlength="50" autocomplete="organization">
                        <p class="maly text">Vyplňte jméno firmy, kterou zastupujete. <br/> Pokud zastupujete více firem, oddělte je čárkou.</p>
                    </div>
                </div>
                <div class="register-info-representative">
                    <p class="text seznam">Info pro reprezentanta</p>
                    <p class="text seznam">Zástupcem firmy se stanete až po schválení organizátorem. O schválení budete informováni e-mailem a pak budete moci upravovat data firmy.</p>
                    <div><p class="ui orange button">Rozumím</p></div>
                </div>
                <div class="ui fluid large buttons">
                    <a class="ui hide button" href="javascript:void(0)">Zavřít</a>
                    <button class="ui positive button" type="submit" name="register" value=1 >Registrovat</button>
                </div>
            </form>
        </div>
    </div>
