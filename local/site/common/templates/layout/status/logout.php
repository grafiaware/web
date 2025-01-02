    <div class="logout">
        <!--<p class="profil-ikona"><a class="link-img" href=""><i class="inverted user circle icon"></i></a></p>-->
        
        <form class="ui form" method="POST" action="">
            <div class="ui icon top left pointing dropdown fluid button">
                <i class="sign out alternate icon"></i>              
            <div class="menu">
                <div class="text nastred">
                    <p class=""><i class="user icon"></i><?= $userName ?></p>
                    <span class="item"></span>
                </div>
                <button class="ui button" type="submit" name="logout" value="1"
                formtarget="_self" formaction='auth/v1/logout'>
                Odhlásit
                </button>
                <button class="ui inverted secondary compact button" type="submit" name="logout" value="1"
                formtarget="_self" formaction='auth/v1/changepassword'>
                Chci si změnit heslo
                </button>
            </div>
            </div>
        </form>
    </div>
