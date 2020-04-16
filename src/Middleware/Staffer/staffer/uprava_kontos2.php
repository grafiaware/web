<?php
use Middleware\Staffer\AppContext;
$handler = AppContext::getDb();

if (isset($_POST['osoba'])) {$osoba=$_POST['osoba'];} else {$osoba='';}
if (isset($_POST['jmeno'])) {$jmeno=$_POST['jmeno'];} else {$jmeno='';}
if (isset($_POST['prijmeni'])) {$prijmeni=$_POST['prijmeni'];} else {$prijmeni='';}
if (isset($_POST['mail'])) {$mail=$_POST['mail'];} else {$mail='';}
if (isset($_POST['tel'])) {$tel=$_POST['tel'];} else {$tel='';}
if (isset($_POST['fax'])) {$fax=$_POST['fax'];} else {$fax='';}
$chyby='';
if ($osoba == '') {$chyby.='<p class="chyba">Došlo ke ztátě kontextu osoby.</p>';}
if ($jmeno =='') {$chyby.='<p class="chyba">Nezadali jste své jméno!</p>';}
if ($prijmeni=='') {$chyby.='<p class="chyba">Nezadali jste své příjmení!</p>';}
if ($mail=='') {$chyby.='<p class="chyba">Nezadali jste svůj e-mail!</p>';}
if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {$chyby.='<p class="chyba">Nesprávná e-mailová adresa!</p>';}
if ($chyby != '') {
    echo 'Proveďte opravu těchto údajů:<br>'.$chyby;
    include 'uprava_kontos.php';
} else {
    $ok = $handler->exec("UPDATE staffer_kontos SET jmeno='$jmeno',prijmeni='$prijmeni',mail='$mail',tel='$tel',fax='$fax' WHERE id='$osoba'");
    if ($ok !== FALSE) {
        echo 'Osoba '.$jmeno.' '.$prijmeni.' upravena';
        include 'prehled_kontos.php';
    } else {
        echo '<p class="chyba">Data nebyla uložena!</p>';
        include 'uprava_kontos.php';
    }
}
?>
