<?php
use Middleware\Staffer\AppContext;
$handler = AppContext::getDb();

$ok=1;
if (isset ($_GET['pozice'])) {$pozice = $_GET['pozice'];} else {$ok=0;}
if (isset ($_GET['odpoved'])) {$odpoved = $_GET['odpoved'];} else {$ok=0;}
if (!$ok) {
    echo '<p class="chyba">Nelze odstanit, došlo ke ztrátě kontextu odpovědi nebo pozice!</a>';
    include 'prehled_vsech_pozic.php';
} else {
    $statement = $handler->query("SELECT zivotopis FROM staffer_odpovedi WHERE id_odpoved='$odpoved'");
    $statement->execute();
    $zaznam = $statement->fetch(PDO::FETCH_ASSOC);
    if ($zaznam['zivotopis'] != '' AND strlen($zaznam['zivotopis'])>5) {
        $zivotopis = $zaznam['zivotopis'];
        $statement = $handler->query("SELECT nazev FROM staffer_zivotopisy WHERE id='$zivotopis'");
        $statement->execute();
        $zaznam = $statement->fetch(PDO::FETCH_ASSOC);
        $cesta = 'files/'.$zaznam['nazev'];
        if (file_exists ($cesta)) {
            unlink ($cesta);
        }
        $handler->exec("DELETE FROM staffer_zivotopisy WHERE id='$zivotopis' limit 1");
    }
    $handler->exec("DELETE FROM staffer_odpovedi WHERE id_odpoved='$odpoved' limit 1");
    include 'prehled_vsech_pozic.php';
}
?>
