<?php
use Middleware\Staffer\AppContext;
$handler = AppContext::getDb();

if (isset($_POST['jmeno'])) {$jmeno=$_POST['jmeno'];} else {$jmeno='';}
if (isset($_POST['prijmeni'])) {$prijmeni=$_POST['prijmeni'];} else {$prijmeni='';}
if (isset($_POST['mail'])) {$mail=$_POST['mail'];} else {$mail='';}
if (isset($_POST['tel'])) {$tel=$_POST['tel'];} else {$tel='';}
if (isset($_POST['fax'])) {$fax=$_POST['fax'];} else {$fax='';}
$chyby='';
if ($jmeno =='') {$chyby.='<p class="chyba">Nezadali jste své jméno!</p>';}
if ($prijmeni=='') {$chyby.='<p class="chyba">Nezadali jste své příjmení!</p>';}
if ($mail=='') {$chyby.='<p class="chyba">Nezadali jste svůj e-mail!</p>';}
if (!(ereg('@[^@]+[.][a-zA-Z]+$', $mail) ) ) {$chyby.='<p class="chyba">Nesprávná e-mailová adresa!</p>';}

if ($chyby != '') {
    echo 'Proveďte opravu těchto údajů:<br>'.$chyby;
    include 'nova_kontos.php';
} else {
    $id = uniqid(rand(), 0);
    $ok = $handler->exec("INSERT INTO staffer_kontos (id,jmeno,prijmeni,mail,tel,fax) VALUES ('$id','$jmeno','$prijmeni','$mail','$tel','$fax')");
    if ($ok) {
        echo 'Osoba '.$jmeno.' '.$prijmeni.' založena';
        include 'prehled_kontos.php';
    } else {
        echo '<p class="chyba">Data nebyla uložena!</p>';
        include 'nova_kontos.php';
    }
}
?>
