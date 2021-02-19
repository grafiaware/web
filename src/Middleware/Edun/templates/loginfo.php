<DIV ID="rs_user">
<?php
        $sess_user = $_SESSION['login']['user']->getCredentials();
echo "Přihlášen je uživatel: <b>" . $sess_user."</b>";
//if ($sess_prava['chpass']) {
//                            echo '<a href="index.php?list=chpass">Změnit heslo</a>';
//                           }?>
</DIV>
<DIV ID="rs_logout">
<?php
echo '<a href="index.php?logout=1">Odhlásit se</a>';
?>
</DIV>
