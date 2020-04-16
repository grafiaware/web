<?php
use Middleware\Staffer\AppContext;
$handler = AppContext::getDb();

//def. konstant
$zaznam = array ();
$db_aktivstart = 'aktiv_lanstart';
$db_aktivstop = 'aktiv_lanstop';
$chyby = '';
//prejimka promennych
if (isset ($_POST['aktiv'])) //upravil Lukáš - původní bylo -> {$zaznam['aktiv'] = $_POST['aktiv'];} else {$zaznam['aktiv'] = 0;}
    {$aktiv = $_POST['aktiv'];} else {$aktiv = 0;}//upravil Lukáš
if (isset ($_POST['aktiv_lanstart'])) {$zaznam[$db_aktivstart] = $_POST['aktiv_lanstart'];} else {$zaznam[$db_aktivstart] = date("Y-m-d");}
if (isset ($_POST['aktiv_lanstop'])) {$zaznam[$db_aktivstop] = $_POST['aktiv_lanstop'];} else {$zaznam[$db_aktivstop] = date("Y-m-d");}
if (isset ($_POST['nazev'])) {$nazev = $_POST['nazev'];} else {$nazev = '';}
if (isset ($_POST['typ'])) {$typ = $_POST['typ'];}

if (isset ($_POST['pozice_s_odmenou'])) {$pozice_s_odmenou = $_POST['pozice_s_odmenou'];}

if (isset ($_POST['nabizime'])) {$nabizime = $_POST['nabizime'];} else {$nabizime = '';}
if (isset ($_POST['pozadavky'])) {$pozadavky = $_POST['pozadavky'];} else {$pozadavky = '';}
if (isset ($_POST['popis'])) {$popis = $_POST['popis'];} else {$popis = '';}
if (isset ($_POST['nastup'])) {$nastup = $_POST['nastup'];} else {$nastup = '';}
if (isset ($_POST['model'])) {$model = $_POST['model'];} else {$model = '';}
if (isset ($_POST['kontakt_id'])) {$kontakt_id = $_POST['kontakt_id'];} else {$kontakt_id = '';}
if (isset ($_POST['pozice'])) {$pozice = $_POST['pozice'];} else {$pozice = '';}
//kontrola chyb
if ($nazev == '') {$chyby = '<p class="chyba">Nezadali jste název pracovní pozice</p>';}
if ($kontakt_id == '') {$chyby.= '<p clas="chyba">Neexistuje kontaktní osoba!</p>';}
if ($pozice == '') {$chyby.= '<p class="chyba">Došlo ke ztrátě kontextu pozice!</p>';}
if ($model != '') {
$model = trim($model);
    if ((substr($model,0,7) != 'http://') AND (substr($model,0,7) != 'HTTP://')) {
        $model='http://'.$model;
    }
}
if ($chyby != '') {
    echo '<p>Opravte následující nedostatky:</p>'.$chyby;
    include 'prehled_vsech_pozic.php';
} else {
    $nabizime = html_entity_decode ($nabizime,ENT_QUOTES,"UTF-8");
    $pozadavky = html_entity_decode ($pozadavky,ENT_QUOTES,"UTF-8");
    $popis = html_entity_decode ($popis,ENT_QUOTES,"UTF-8");
    $editor = $_SESSION['login']['user'];
    $ok = $handler->exec("UPDATE staffer_pozice SET nazev='$nazev',typ='$typ', pozice_s_odmenou='$pozice_s_odmenou',
        nabizime='$nabizime',pozadavky='$pozadavky',popis='$popis',nastup='$nastup',model='$model',
        aktiv='$aktiv',aktiv_start='$zaznam[$db_aktivstart]',aktiv_stop='$zaznam[$db_aktivstop]',
        kontakt_id='$kontakt_id',editor='$editor'
        WHERE id='$pozice'");
    if ($ok) {
        echo 'Pozice '.$nazev.' upravena';
        include 'prehled_vsech_pozic.php';
    } else {
        echo '<p class="chyba">Data nebyla uložena!</p>';
        include 'prehled_vsech_pozic.php';
    }
}
?>
