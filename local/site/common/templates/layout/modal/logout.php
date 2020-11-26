    <div class="logout">
        <form class="ui form" method="POST" action="">
            <div class="ui icon top left pointing dropdown button">
              <i class="sign out alternate icon"></i>
              <div class="menu">
                <div class="item header"><p><i class="user icon"></i><?= $userName ?></p></div>
                <button class="ui button" type="submit" name="logout" value="1"
                       formtarget="_self"formaction='auth/v1/logout'>
                    Odhlásit
                </button>
              </div>
            </div>
        </form>
    </div>
