            <div class="logout">
                <p><?= $userName ?></p>
                <form class="ui inverted form centered" method="POST" action="">
                    <button class="ui mini icon button" type="submit" name="logout" value="1" title="Odhlásit se" formtarget="_self"
                        formaction='auth/v1/logout'><i class="sign out alternate large icon"></i>
                    </button>
                </form>
            </div>