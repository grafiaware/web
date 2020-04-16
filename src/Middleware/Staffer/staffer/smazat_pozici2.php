<?php
use Middleware\Staffer\AppContext;
$handler = AppContext::getDb();

if (isset ($_GET['pozice'])) {
    $pozice = $_GET['pozice'];
    $zivotopisy = '';
    $i = 0;
    $statement = $handler->query("SELECT zivotopis FROM staffer_odpovedi WHERE id_pozice='$pozice'");
    $statement->execute();
    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $zaznam) {
        if ($zaznam['zivotopis']!='' AND strlen($zaznam['zivotopis'])>5) {
            if (!$i) {
                $zivotopisy.= "id='".$zaznam['zivotopis']."'";
            }
            if ($i) {
                $zivotopisy.= " OR id='".$zaznam['zivotopis']."'";
            }
            $i++;
        }
    }
    if ($zivotopisy) {
        $statement = $handler->query("SELECT nazev FROM staffer_zivotopisy WHERE $zivotopisy");
        $statement->execute;
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $zaznam) {
            $cesta = 'files/'.$zaznam['nazev'];
            if (file_exists ($cesta)) {unlink ($cesta);}
        }
        $handler->exec("DELETE FROM staffer_zivotopisy WHERE $zivotopisy");
    }
    $handler->exec("DELETE FROM staffer_odpovedi WHERE id_pozice='$pozice'");
    $handler->exec("DELETE FROM staffer_pozice WHERE id='$pozice'");
    include 'prehled_vsech_pozic.php';
} else {
    echo '<p class="chyba">Nelze odstanit, došlo ke ztrátě kontextu pozice!</a>';
    include 'prehled_vsech_pozic.php';
}
?>
