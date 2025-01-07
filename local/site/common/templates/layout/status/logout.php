    <?php
        use Site\ConfigurationCache;
    ?>

    <div class="logout">
        <!--<p class="profil-ikona"><a class="link-img" href=""><i class="inverted user circle icon"></i></a></p>-->
        
            <div class="ui icon top left pointing dropdown button item">
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
                    <p class="ui basic fluid black button btn-zmena-hesla">
                        Chci si změnit heslo
                    </p>
                    
                     <div class="ui mini page modal transition hidden zmena-hesla">
                        <i class="white close icon"></i>
                        <div class="content">
                            <form class="ui form" method="POST" action="">
                                <div class="field">
                                    <label>Vaše aktuální heslo</label>
                                    <input type="password" name="<?=ConfigurationCache::auth()['fieldNameHesloStare']?>" placeholder="Aktuální heslo" required>
                                </div>
                                <div class="field">
                                    <label>Nové heslo</label>
                                    <input type="password" name="<?=ConfigurationCache::auth()['fieldNameHeslo']?>" placeholder="Nové heslo"
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
