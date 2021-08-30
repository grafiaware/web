<?php
use Middleware\Staffer\AppContext;
$handler = AppContext::getDb();

if (isset ($_GET['pozice'])) {
    $pozice = $_GET['pozice'];
    $statement = $handler->query("SELECT nazev FROM staffer_pozice WHERE id='$pozice'");
    $statement->execute();
    $zaznam = $statement->fetch(PDO::FETCH_ASSOC);
    echo '<center><DIV class="rs_staf_alert">Opravdu chcete nenávratně odstranit pozici<br><b>'.$zaznam['nazev'].'</b><br>včetně veškerých došlých odpovědí a životopisů?<br><br>
                <a href="?list=smazat_pozici2&pozice='.$pozice.'&app=staffer">&nbsp;ANO&nbsp;</a>
                <a href="?list=prehled_vsech_pozic&app=staffer">&nbsp;NE&nbsp;</a></DIV></center>';
} else {
    echo '<p class="chyba">Nelze odstanit, došlo ke ztrátě kontextu pozice!</a>';
    include 'prehled_vsech_pozic.php';
}
?>
