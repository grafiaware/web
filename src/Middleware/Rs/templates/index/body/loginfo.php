<!--<DIV ID="rs_user">
< ?php
$sess_user = $_SESSION['login']['user'];
echo "Přihlášen je uživatel: <b>" . $sess_user."</b>";
if ($sess_prava['chpass']) {
                            echo '<a href="index.php?list=chpass">Změnit heslo</a>';
                           }?>
</DIV>
<DIV ID="rs_logout">
        <div class="">
            <form class="" method="POST" action="">
                    <button class="" type="submit" name="logout" value="1" formtarget="_self">
                        <i class=""></i>
                        Odhlásit se
                    </button>
            </form>
        </div>
</DIV>-->
<div class="fifteen wide mobile three wide tablet three wide computer column center aligned">
    <img src="<?= Middleware\Web\AppContext::getAppPublicDirectory().'grafia/img/grafia_logo.gif' ?>" alt="Grafia, s.r.o. Plzeň"/>
</div>
<div class="fifteen wide mobile seven wide tablet nine wide computer column center aligned middle aligned">
    <p>Redakční systém</p>
</div>
<div class="fifteen wide mobile five wide tablet three wide computer column right aligned">
    <?php
        $sess_user = $_SESSION['security']['user']->getCredential();
        echo "<p><i class='user icon'></i> <b>" . $sess_user."</b> | ";
        if ($sess_prava['chpass']) {
            echo '<a href="index.php?list=chpass">Změnit heslo</a></p>';
        }
    ?>
    <form class="" method="POST" action="">
        <button class="ui small button" type="submit" name="logout" value="1" formtarget="_self">
            Odhlásit se
        </button>
    </form>
</div>
