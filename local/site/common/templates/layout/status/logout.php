    <?php
        use Site\ConfigurationCache;
    ?>

    <div class="logout">
        <!--<p class="profil-ikona"><a class="link-img" href=""><i class="inverted user circle icon"></i></a></p>-->
        
            <div class="ui icon top left pointing dropdown button">
                <i class="sign out alternate icon"></i>              
            <div class="menu">
                <div class="text nastred">
                    <p class=""><i class="user icon"></i><?= $userName ?></p>
                    <span class="item"></span>
                </div>
                <form class="ui form" method="POST" action="">
                    <button class="ui fluid button" type="submit" name="logout" value="1"
                    formtarget="_self" formaction='auth/v1/logout'>
                    Odhlásit
                    </button>
                </form>
                <div class="item"> 
                    <p class="ui basic fluid black button">
                        Chci si změnit heslo</p>
                    <div class="menu">
                        <form class="ui form" method="POST" action=""><
                            <div class="field">
                                <label>Vaše aktuální heslo</label>
                                <input class="notRequired" type="password" name="<?=ConfigurationCache::auth()['fieldNameHeslo']?>" placeholder="Aktuální heslo" required>
                            </div>
                            <div class="field">
                                <label>Nové heslo</label>
                                <input class="notRequired" type="password" name="<?=ConfigurationCache::auth()['fieldNameHeslo']?>" placeholder="Nové heslo" required>
                            </div>
                            <button class="ui inverted secondary fluid button" type="submit" name="logout" value="1"
                                formtarget="_self" formaction='auth/v1/changepassword'>Odeslat</button>
                        </form>
                    </div>
                </div>
            </div>
            </div>
    </div>
