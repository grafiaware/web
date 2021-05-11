    <div class="logout">
        <!-- na veletrhprace.online  :   profil návtěvníka web/v1/page/item/6062d0e00190e      ;     profil vystavovatele web/v1/page/item/6062dfc9a10ab -->
        <!--<p class="profil-ikona"><a class="link-img" href=""><i class="inverted user circle icon"></i></a></p>-->
        <form class="ui form" method="POST" action="">
            <div class="ui icon top left pointing dropdown button">
              <i class="sign out alternate icon"></i>
              <div class="menu">
                <div class="item header"><p><i class="user icon"></i><?= $loginName ?></p></div>
                <button class="ui button" type="submit" name="logout" value="1"
                       formtarget="_self"formaction='auth/v1/logout'>
                    Odhlásit
                </button>
              </div>
            </div>
        </form>
    </div>
