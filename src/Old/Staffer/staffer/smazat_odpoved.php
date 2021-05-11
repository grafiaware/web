<?php
use Middleware\Staffer\AppContext;
$handler = AppContext::getDb();

$ok=1;
if (isset ($_GET['pozice'])) {$pozice = $_GET['pozice'];} else {$ok=0;}
if (isset ($_GET['odpoved'])) {$odpoved = $_GET['odpoved'];} else {$ok=0;}
if (!$ok) {
    echo '<p class="chyba">Nelze odstranit, došlo ke ztrátě kontextu odpovědi nebo pozice!</a>';
    include 'prehled_vsech_pozic.php';
} else {
    $statement = $handler->query("SELECT jmeno,prijmeni,titul FROM staffer_odpovedi WHERE id_odpoved='$odpoved'");
    $statement->execute();
    $zaznam = $statement->fetch(PDO::FETCH_ASSOC);
    echo '<center><DIV class="rs_staf_alert">Opravdu chcete odstranit odpověď od:<br><b>'.$zaznam['jmeno'].' '.$zaznam['prijmeni'].'</b>?<br><br>
                              <a href="?list=smazat_odpoved2&pozice='.$pozice.'&odpoved='.$odpoved.'&app=staffer">&nbsp;ANO&nbsp;</a>
                              <a href="?list=cteni_odpovedi&odpoved='.$odpoved.'&pozice='.$pozice.'&app=staffer">&nbsp;NE&nbsp;</a></DIV></center>';
}
?>
