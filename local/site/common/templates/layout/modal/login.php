    <form class="ui inverted form centered" method="POST" action="auth/v1/login">
        <div class="ui icon top left pointing dropdown button loginEnterKey">
            <i class="user icon"></i>
            <div class="menu">
                <div class="item header"><p><i class="user icon"></i>Přihlásit se</p></div>
                <div class="ui input">
                    <input type="text" name="<?=$jmenoFieldName?>" placeholder="Jméno" required>
                </div>
                <div class="ui input">
                    <input type="password" name="<?=$hesloFieldName?>" placeholder="Heslo" required>
                </div>
                <button class="ui positive button" type="submit" name="login" value=1>Přihlásit</button>
          </div>
        </div>
    </form>
