    <?php
        use Site\ConfigurationCache;
        $osobniJmeno = \Application\Feature\FeatureFlags::fromConfiguration()->isEnabled('vp.osobni-jmeno');  // A8
    ?>

    <div class="logout">
        <!--<p class="profil-ikona"><a class="link-img" href=""><i class="inverted user circle icon"></i></a></p>-->
        
            <div class="ui icon top left pointing dropdown button item">
                <i class="sign out alternate icon"></i>              
                <div class="menu">
                    <div class="text nastred">
                        <p class=""><i class="user icon"></i><?= htmlspecialchars((string) $userName) ?></p>
                        <span class="item"></span>
                    </div>
                    <form class="ui form" method="POST" action="">
                        <button class="ui fluid button" type="submit" name="logout" value="1"
                        formtarget="_self" formaction='auth/v1/logout'>
                        Odhlásit
                        </button>
                    </form>
                    <?php if ($osobniJmeno): ?>
                    <p class="ui basic fluid black button btn-zmena-jmena">
                        <?= ($displayName ?? '') === '' ? 'Zadat své jméno' : 'Změnit jméno' ?>
                    </p>
                    <div class="ui mini page modal transition hidden zmena-jmena">
                        <i class="white close icon"></i>
                        <div class="content">
                            <form class="ui form" method="POST" action="">
                                <div class="field">
                                    <label>Vaše jméno</label>
                                    <input type="text" name="displayName" maxlength="100" autocomplete="name"
                                           placeholder="Např. Jan Novák" value="<?= htmlspecialchars((string) ($displayName ?? '')) ?>">
                                    <p class="maly text">Zobrazuje se místo přihlašovacího jména <?= htmlspecialchars((string) ($loginName ?? '')) ?>. Prázdné pole jméno odebere.</p>
                                </div>
                                <button class="ui positive fluid button" type="submit" name="changedisplayname" value="1"
                                    formtarget="_self" formaction="auth/v1/displayname">Uložit</button>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>
                    <p class="ui basic fluid black button btn-zmena-hesla">
                        Chci si změnit heslo
                    </p>
                    
                     <div class="ui mini page modal transition hidden zmena-hesla">
                        <i class="white close icon"></i>
                        <div class="content">
                            <form class="ui form" method="POST" action="">
                                <?php if (\Application\Feature\FeatureFlags::fromConfiguration()->isEnabled('vp.registrace-emailem')): ?>
                                <div class="field">
                                    <label>Vaše aktuální heslo</label>
                                    <input type="password" name="<?=ConfigurationCache::auth()['fieldNameHesloStare']?>" placeholder="Aktuální heslo" autocomplete="current-password">
                                    <p class="maly text">Nevyplňujte, pokud heslo nemáte nebo jste se právě přihlásili odkazem z e-mailu.</p>
                                </div>
                                <?php else: ?>
                                <div class="field">
                                    <label>Vaše aktuální heslo</label>
                                    <input type="password" name="<?=ConfigurationCache::auth()['fieldNameHesloStare']?>" placeholder="Aktuální heslo" required>
                                </div>
                                <?php endif; ?>
                                <div class="field">
                                    <label>Nové heslo</label>
                                    <input type="password" name="<?=ConfigurationCache::auth()['fieldNameHeslo']?>" placeholder="Nové heslo" autocomplete="new-password"
                                        pattern="<?=ConfigurationCache::auth()['passwordPattern']?>"
                                        title ="<?=ConfigurationCache::auth()['passwordInfo']?>"
                                        required >
                                </div>
                                <button class="ui positive fluid button" type="submit" name="changepassword" value="1"
                                    formtarget="_self" formaction='auth/v1/changepassword'>Odeslat</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </div>
