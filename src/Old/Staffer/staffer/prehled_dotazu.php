<?php
use Middleware\Staffer\AppContext;
$handler = AppContext::getDb();

if (isset($_GET['pozice'])) {
    $pozice = $_GET['pozice'];
    $statement = $handler->query("SELECT nazev FROM staffer_pozice WHERE id='$pozice'");
    $statement->execute();
    $zaznam = $statement->fetch(PDO::FETCH_ASSOC);
    echo '<H3>Přehled dotazů na pozici '.$zaznam['nazev'].'</H3>';

    $statement = $handler->query("SELECT id_odpoved,jmeno,prijmeni,titul,vlozeno,precteno FROM staffer_odpovedi
            WHERE id_pozice='$pozice'  and dotaz!=''  ORDER BY vlozeno DESC");
    $statement->execute();
    if (!$statement->rowCount()) {
        echo '<p>Není uložen žádný dotaz.</p>';
    }

    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $zaznam) {
        echo '<fieldset><legend>';
        if ($zaznam['precteno'] == 0) {
            echo '<img src="app/staffer/img/neprecteno.png"> nepřečteno ';
        }
        //else {echo '<span style="color:#D0D0BF">přečteno </span>';}
        echo '</legend>';

        echo '<DIV id="rs_staf_prehled_siroky">';
        echo 'Jméno&nbsp;a&nbsp;příjmení:<br><b>'.$zaznam['titul'].' '.$zaznam['jmeno'].' '.$zaznam['prijmeni'].'</b>';
        echo '</DIV>';

        echo '<DIV id="rs_staf_prehled_tl_l">';
        echo '<a href="?list=cteni_odpovedi&odpoved='.$zaznam['id_odpoved'].'&pozice='.$pozice.'&app=staffer"><img src="app/staffer/img/cist.png"><br>Číst</a>';
        echo '</DIV>';

        echo '<DIV id="rs_staf_prehled_tl_l">';
        echo '<a href="?list=cteni_odpovedi&odpoved='.$zaznam['id_odpoved'].'&pozice='.$pozice.'&tab=1&app=staffer"><img src="app/staffer/img/cist.png"><br>Číst jako tabulku</a>';
        echo '</DIV>';

        echo '<DIV id="rs_staf_prehled_siroky">';
        $zalozeno = $zaznam['vlozeno'];
        $zalozeno = $zalozeno{8}.$zalozeno{9}.'. '.$zalozeno{5}.$zalozeno{6}.'. '.$zalozeno{0}.$zalozeno{1}.$zalozeno{2}.$zalozeno{3};
        echo 'Dotaz vložen:<br><b>'.$zalozeno.'</b>';
        echo '</DIV>';

        echo '<DIV id="rs_staf_prehled_tl_r" >';
        echo '<a href="?list=smazat_odpoved&pozice='.$pozice.'&odpoved='.$zaznam['id_odpoved'].'&app=staffer"><img src="app/staffer/img/smazat.png"><br>Smazat</a>';
        echo '</DIV>';
        echo '</fieldset><br>';
    }
} else {
    echo '<p class="chyba">Neexistuje kontext pozice!</p>';
}
?>
