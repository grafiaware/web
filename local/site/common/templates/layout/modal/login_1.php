<button class="ui mini page icon button">
    <i class="user icon"></i>
</button>

<div class="ui page dimmer transition hidden">
    <div class="content">
        <div class="center">
            <h2>Přihlásit se</h2>
            <form class="ui inverted form centered" method="POST" action="auth/v1/login">
                <div class="field">
                    <label>Přihlašovací jméno</label>
                    <input type="text" name="<?=
$jmenoFieldName?>" placeholder="Jméno" required>
                </div>
                <div class="field">
                    <label>Heslo</label>
                    <input type="password" name="<?=
        $hesloFieldName?>" placeholder="Heslo" required>
                </div>
                <div class="ui big buttons">
                    <a class="ui hide button" href="javascript:void(0)">Zpět</a>
                    <div class="or" data-text="×"></div>
                    <button class="ui positive button" type="submit" name="login" value=1>Přihlásit</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $('.ui.page.button').click(function(){
       $('.page.dimmer').dimmer('show');
    });
    $('.ui.hide.button').click(function(){
       $('input').removeAttr("required");
       $('.page.dimmer').dimmer('hide');
    });
</script>
