<?php
use Middleware\Staffer\AppContext;
$handler = AppContext::getDb();

$zivotopisy = array();
$odpovedi = array();
$statement = $handler->query("SELECT id FROM staffer_zivotopisy");
$statement->execute();
foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $zaznam) {
    $zivotopisy[] = $zaznam['id'];
}

$statement = $handler->query("SELECT zivotopis FROM staffer_odpovedi WHERE CHAR_LENGTH(zivotopis) > 3");
$statement->execute();
foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $zaznam) {
    $odpovedi[] = $zaznam['zivotopis'];
}

$statement = $handler->query("SELECT zivotopis_nepovinny FROM staffer_odpovedi WHERE CHAR_LENGTH(zivotopis_nepovinny) > 3");
$statement->execute();
foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $zaznam) {
    $odpovedi[] = $zaznam['zivotopis_nepovinny'];
}

$smazat = '';
$i=0;
foreach ($zivotopisy as $zivotopis) {
    if (!in_array ($zivotopis,$odpovedi)) {
            $smazat[] = "id='".$zivotopis."'";
    }
}
if ($smazat != '') {
    $smazat = implode(' OR ',$smazat);
    $statement = $handler->query("SELECT nazev FROM staffer_zivotopisy WHERE $smazat");
    $statement->execute();
    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $zaznam) {
        $cesta = AppContext::getAppPublicDirectory().'files/'.$zaznam['nazev'];
        if (file_exists($cesta)) {
            unlink($cesta);
        }
    }

    MySQL_Query("DELETE FROM staffer_zivotopisy WHERE $smazat");
}
?>
