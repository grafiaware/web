<?php
use Middleware\Staffer\AppContext;
$handler = AppContext::getDb();

if (isset($_GET['osoba'])) {
    $osoba=$_GET['osoba'];
    $statement = $handler->query("SELECT jmeno,prijmeni FROM staffer_kontos WHERE id='$osoba'");
    $statement->execute();
    $zaznam = $statement->fetch(PDO::FETCH_ASSOC);
    $jmeno = $zaznam['jmeno'];
    $prijmeni = $zaznam['prijmeni'];
    $statement = $handler->query("SELECT nazev FROM staffer_pozice WHERE kontakt_id='$osoba'");
    $statement->execute();
    if (!$statement->rowCount()) {
        echo '<center><DIV class="rs_staf_alert">Opravdu chcete odstranit osobu<br><b>'.$jmeno.' '.$prijmeni.'</b>?<br><br>
            <a href="?list=smazat_kontos2&osoba='.$osoba.'&app=staffer">&nbsp;ANO&nbsp;</a>
            <a href="?list=prehled_kontos&app=staffer">&nbsp;NE&nbsp;</a></DIV></center>';
    } else {
        echo '<center><DIV class="rs_staf_alert">Osobu '.$jmeno.' '.$prijmeni.' nelze odstranit, protože je vedena jako kontaktní osoba u pozice:<br><b>';
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $zaznam) {
            echo $zaznam['nazev'].'<br>';
        }
        echo '</b>Změňte u těchto pozic kontaktní osobu!</DIV></center>';
    }
} else {
    echo'<p class="chyba">Došlo ke ztrátě kontextu osoby!</p>';
    include 'prehled_kontos.php';
}
?>
