<?php
use Middleware\Staffer\AppContext;
$handler = AppContext::getDb();

if (isset ($_GET['pozice'])) {
    $pozice = $_GET['pozice'];
    $db_aktivstart = 'aktiv_start';
    $db_aktivstop = 'aktiv_stop';

    $statement = $handler->query("SELECT * FROM staffer_pozice WHERE id = '$pozice' limit 1");
    $statement->execute();
    $zaznam = $statement->fetch(PDO::FETCH_ASSOC);
    $nazev = $zaznam['nazev'];
    $typ = $zaznam['typ'];
    $nabizime = $zaznam['nabizime'];
    $pozadavky = $zaznam['pozadavky'];
    $popis = $zaznam['popis'];
    $pozice_s_odmenou = $zaznam['pozice_s_odmenou'];

    $nastup = $zaznam['nastup'];
    $model = $zaznam['model'];
    $kontakt_id = $zaznam['kontakt_id'];
    $zalozeno = $zaznam['zalozeno'];
    $zalozeno = $zalozeno{8}.$zalozeno{9}.'. '.$zalozeno{5}.$zalozeno{6}.'. '.$zalozeno{0}.$zalozeno{1}.$zalozeno{2}.$zalozeno{3};
    $zmeneno = $zaznam['zmeneno'];
    $zmeneno = $zmeneno{8}.$zmeneno{9}.'. '.$zmeneno{5}.$zmeneno{6}.'. '.$zmeneno{0}.$zmeneno{1}.$zmeneno{2}.$zmeneno{3};

    echo '<H3>Úprava pozice '.$nazev.'</H3>';
    echo '<form method="POST" enctype="multipart/form-data" action="?list=uprava_pozice2&app=staffer">';
    include 'pozice.php';
    echo '<input type="hidden" name="pozice" value="'.$pozice.'">';
    echo '<fieldset><legend>Historie</legend><br>';
    echo 'Datum založení pozice: '.$zalozeno.'&nbsp;&nbsp;&nbsp; Datum poslední změny: '.$zmeneno.'&nbsp;&nbsp;&nbsp; Změnu provedl uživatel: '.$zaznam['editor'];
    echo '<br><br></fieldset><br>';
    echo '<input type="submit" value="Uložit" name="save"><br><br>';
    echo '</form>';
} else {
    echo '<p class="chyba">Došlo ke ztrátě kontextu pozice</p>';
}
?>
