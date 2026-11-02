<?php
use Site\ConfigurationCache;
use Application\Feature\FeatureFlags;

$features = FeatureFlags::fromConfiguration();
$loginByEmail = $features->isEnabled('vp.registrace-emailem');   // A1
$loginLink = $features->isEnabled('vp.prihlaseni-odkazem');      // A2
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
                <?php if ($loginByEmail): ?>
                <div class="field">
                    <label>E-mail nebo přihlašovací jméno</label>
                    <input type="text" name="<?=ConfigurationCache::auth()['fieldNameJmeno']?>" placeholder="E-mail nebo jméno"
                           autocomplete="username" autocapitalize="none" spellcheck="false" required>
                </div>
                <div class="field">
                    <label>Heslo</label>
                    <input type="password" name="<?=ConfigurationCache::auth()['fieldNameHeslo']?>" placeholder="Heslo"
                           autocomplete="current-password" required>
                </div>
                <?php else: ?>
                <div class="field">
                    <label>Přihlašovací jméno</label>
                    <input type="text" name="<?=ConfigurationCache::auth()['fieldNameJmeno']?>" placeholder="Jméno" required>
                </div>
                <div class="field">
                    <label>Heslo</label>
                    <input type="password" name="<?=ConfigurationCache::auth()['fieldNameHeslo']?>" placeholder="Heslo" required>
                </div>
                <?php endif; ?>
                <div class="ui fluid large buttons">
                    <a class="ui hide button" href="javascript:void(0)">Zavřít</a>
                    <button class="ui positive button" tabindex="1" type="submit" name="login" value=1>Přihlásit</button>
                </div>
                <?php if (!$loginLink): ?>
                <p class="text">
                    <button class="ui basic fluid button password-btn" tabindex="0" type="submit" name="forgottenpassword" value=1 formaction="auth/v1/forgottenpassword">Zapomněla/zapomněl jsem heslo</button>
                </p>
                <?php endif; ?>
            </form>
            <?php if ($loginLink): ?>
            <div class="ui divider"></div>
            <form class="ui form centered" method="POST" action="auth/v1/loginlink">
                <p class="text">Zapomněli jste heslo, nebo ho nemáte? Pošleme Vám přihlašovací odkaz.</p>
                <div class="field">
                    <label>E-mail</label>
                    <input type="email" name="email" placeholder="vas@email.cz" autocomplete="email" inputmode="email" required>
                </div>
                <button class="ui basic fluid button" type="submit" name="loginlink" value="1">Poslat přihlašovací odkaz</button>
            </form>
            <?php endif; ?>
        </div>
    </div>


