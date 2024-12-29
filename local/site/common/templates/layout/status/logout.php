    <div class="logout">
        <!--<p class="profil-ikona"><a class="link-img" href=""><i class="inverted user circle icon"></i></a></p>-->
                <p class="text velky zadne-okraje"><i class="user icon"></i><?= $userName ?></p>
        
        <form class="ui form" method="POST" action="">
            <div class="ui icon top left pointing dropdown button">
                <p class=""><i class="user icon"></i><?= $userName ?></p>
                <i class="sign out alternate icon"> </i>               
            <div class="menu">
                <div class="text nastred">
                    <p class=""><i class="user icon"></i><?= $userName ?></p>
                    <span class="item"></span>
                </div>
                <button class="ui button" type="submit" name="logout" value="1"
                formtarget="_self" formaction='auth/v1/logout'>
                Odhlásit
                </button>
            </div>
            </div>
        </form>
    </div>
