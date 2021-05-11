<?php
use Middleware\Staffer\AppContext;

//def. konstant
$zaznam = array ();
$db_aktiv = 'aktiv';
$db_aktivstart = 'aktiv_lanstart';
$db_aktivstop = 'aktiv_lanstop';
$chyby = '';
//prejimka promennych
if (isset ($_POST['aktiv'])) {$zaznam[$db_aktiv] = $_POST['aktiv'];} else {$zaznam[$db_aktiv] = 0;}
if (isset ($_POST['aktiv_lanstart']) AND $_POST['aktiv_lanstart']) {
    $zaznam[$db_aktivstart] = $_POST['aktiv_lanstart'];
} else {
    $zaznam[$db_aktivstart] = date("Y-m-d");
}
if (isset ($_POST['aktiv_lanstop']) AND $_POST['aktiv_lanstop']) {$zaznam[$db_aktivstop] = $_POST['aktiv_lanstop'];} else {$zaznam[$db_aktivstop] = date("Y-m-d");}
if (isset ($_POST['nazev'])) {$nazev = $_POST['nazev'];} else {$nazev = '';}
if (isset ($_POST['typ'])) {$typ = $_POST['typ'];} else {$typ = 1;}
if (isset ($_POST['pozice_s_odmenou'])) {$pozice_s_odmenou = $_POST['pozice_s_odmenou'];} else {$pozice_s_odmenou = 0;}
if (isset ($_POST['nabizime'])) {$nabizime = $_POST['nabizime'];} else {$nabizime = '';}
if (isset ($_POST['pozadavky'])) {$pozadavky = $_POST['pozadavky'];} else {$pozadavky = '';}
if (isset ($_POST['popis'])) {$popis = $_POST['popis'];} else {$popis = '';}
if (isset ($_POST['nastup'])) {$nastup = $_POST['nastup'];} else {$nastup = '';}
if (isset ($_POST['model'])) {$model = $_POST['model'];} else {$model = '';}
if (isset ($_POST['kontakt_id'])) {$kontakt_id = $_POST['kontakt_id'];} else {$kontakt_id = '';}
//kontrola chyb
if ($nazev == '') {$chyby = '<p class="chyba">Nezadali jste název nové pracovní pozice</p>';}
if ($kontakt_id == '') {$chyby.= '<p clas="chyba">Neexistuje kontaktní osoba!</p>';}
if ($model != '') {
    $model = trim($model);
    if ((substr($model,0,7) != 'http://') AND (substr($model,0,7) != 'HTTP://')) {
        $model='http://'.$model;
    }
}
if ($chyby != '') {
    echo '<p>Opravte následující nedostatky:</p>'.$chyby;
    include 'nova_pozice.php';
} else {
    $nabizime = html_entity_decode ($nabizime,ENT_QUOTES,"UTF-8");
    $pozadavky = html_entity_decode ($pozadavky,ENT_QUOTES,"UTF-8");
    $popis = html_entity_decode ($popis,ENT_QUOTES,"UTF-8");
    $zalozeno = date("Y-m-d H:i:s");
    $editor = $_SESSION['login']['user'];
    $handler = AppContext::getDb();
    try {
        $handler->beginTransaction();
        do {
            $id = uniqid();
            $stmt = $handler->prepare(
                    "SELECT id FROM staffer_pozice
                    WHERE id = :id LOCK IN SHARE MODE");   //nelze použít LOCK TABLES - to commitne aktuální transakci!
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } while ($stmt->rowCount());
        $statement = $handler->query("INSERT INTO staffer_pozice (id,nazev,typ, pozice_s_odmenou, nabizime,pozadavky,popis,nastup,model,
            aktiv,aktiv_start,aktiv_stop,kontakt_id,zalozeno,editor)
            VALUES ('$id','$nazev','$typ', '$pozice_s_odmenou','$nabizime','$pozadavky','$popis','$nastup','$model',
            '$zaznam[$db_aktiv]','$zaznam[$db_aktivstart]','$zaznam[$db_aktivstop]','$kontakt_id','$zalozeno','$editor')");
        $statement->execute();
        $handler->commit();
        $ok = TRUE;
    } catch(Exception $e) {
        $dbhTransact->rollBack();
        $ok = FALSE;
    }
    if ($ok) {
        echo 'Pozice '.$nazev.' založena';
        include 'prehled_vsech_pozic.php';
    } else {
        echo '<p class="chyba">Data nebyla uložena!</p>';
        echo "<pre>".print_r($handler->errorInfo(), TRUE)."</pre>";
        include 'nova_pozice.php';
    }
}
?>
