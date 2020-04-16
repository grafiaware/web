<?php
use Middleware\Staffer\AppContext;
$handler = AppContext::getDb();

if (!isset ($_GET['tab'])) {


        if (isset($_GET['pozice'])) {$pozice = $_GET['pozice'];}
        else {echo '<p class="chyba">Došlo ke ztrátě kontextu pozice</p>';}
        if (isset($_GET['odpoved'])) {$odpoved = $_GET['odpoved'];}
        else {echo '<p class="chyba">Došlo ke ztrátě kontextu odpovedi</p>';}
        if (isset ($_GET['krok'])) {$krok=$_GET[krok];} else {$krok = 2;}

        $pole=array();

        $statement = $handler->query("select nazev,typ FROM staffer_pozice where id='$pozice'");
        $statement->execute();
        $zaznam = $statement->fetch(PDO::FETCH_ASSOC);
        $nazev_pozice = $zaznam['nazev'];
        $formular = $zaznam['typ'];

        $statement_pom = $handler->query("select dotaz FROM staffer_odpovedi where id_odpoved='$odpoved'");  //zda jde prihlasku nebo dotaz
        $statement_pom->execute();
        $je_to_dotaz_pom = $statement_pom->fetch(PDO::FETCH_ASSOC);
        if ($je_to_dotaz_pom ['dotaz'] != "") {
            $formular = konstStaffer_webdotaz; // "webdotaz";
            echo '<h3>Dotaz uchazeče na pozici: '.$nazev_pozice.'</h3>';
        }
        else {
            echo '<h3>Přihláška uchazeče na pozici: '.$nazev_pozice.'</h3>';
        }


        $statement = $handler->query("select pole,nazev FROM staffer_forms where $formular='1'");
        $statement->execute();
        $i=0;
        $sloupce = '';
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $zaznam) {
          $pole [$zaznam['pole']] = array();
          $pole [$zaznam['pole']][0] = $zaznam['nazev'];
          if($i) {$sloupce.= ','.$zaznam['pole'];} else {$sloupce.= $zaznam['pole'];}

          $i++;
        }


        $i=0;
        $statement = $handler->query("select $sloupce FROM staffer_odpovedi where id_odpoved='$odpoved'");
        $statement->execute();

        $pole = $statement->fetch(PDO::FETCH_ASSOC);
        $klice = array_keys($pole);
        foreach ($klice as $klic) {
          $pole[$klic][1] = $zaznam[$klic];
        }

        //echo "<br>formular:" . $formular;
        //echo "<br>pole:";
        //print_r ($pole) ;

        include 'webcontents/def_pole.php';
        $add = 'forms/'.$formular.'.php';
        include $add;

}

else {

        if (isset($_GET['pozice'])) {$pozice = $_GET['pozice'];}
        else {echo '<p class="chyba">Došlo ke ztrátě kontextu pozice</p>';}
        if (isset($_GET['odpoved'])) {$odpoved = $_GET['odpoved'];}
        else {echo '<p class="chyba">Došlo ke ztrátě kontextu odpovedi</p>';}

        $pole=array();
        function vypis_polozky($polozka,$odpoved)
        {
          echo '<tr><td id="rs_staf_odpoved_pol">'.$polozka.': </td>';
          echo '<td id="rs_staf_odpoved_odp">';
          if ($odpoved) {echo '<b>'.$odpoved.'</b>';}
          else {echo '<i>uchazeč na tuto položku neodpověděl</i>';}
          echo '</td></tr>';
        }

        include  '../../data.inc.php' ;
        $vysledek = MySQL_Query("select nazev,typ FROM staffer_pozice where id='$pozice'");
        $zaznam = MySQL_Fetch_Array($vysledek);
        $nazev_pozice = $zaznam['nazev'];

        $formular = $zaznam['typ'];

        $vysledek_pom = MySQL_Query("select dotaz FROM staffer_odpovedi where id_odpoved='$odpoved'");  //zda jde prihlasku nebo dotaz
        $je_to_dotaz_pom = MySQL_Fetch_Array($vysledek_pom);
        if ($je_to_dotaz_pom ['dotaz'] != "") {
            $formular = konstStaffer_webdotaz ;   // "webdotaz";
            // echo '<h3>Dotaz na pozici: '.$nazev_pozice.'</h3>';
            echo '<h3>Dotaz uchazeče '.' na pozici: '.$nazev_pozice.'</h3>';
        }
        else {
            echo '<h3>Přihláška uchazeče '. ' na pozici: '.$nazev_pozice.'</h3>';
        }


        $vysledek = MySQL_Query("select pole,nazev FROM staffer_forms where $formular='1'");
        $i=0;
        $sloupce = '';
        WHILE ($zaznam = MySQL_Fetch_Array($vysledek)) {
           $pole [$i] = array();
           $pole [$i][0] = $zaznam['pole'];
           $pole [$i][1] = $zaznam['nazev'];
           if($i) {$sloupce.= ','.$zaznam['pole'];} else {$sloupce.= $zaznam['pole'];}
           $i++;
        }
        $i=0;
        $vysledek = MySQL_Query("select $sloupce FROM staffer_odpovedi where id_odpoved='$odpoved'");
        $zaznam = MySQL_Fetch_Array($vysledek);


        echo '<fieldset><legend>Formulář</legend>';
        echo '<table id="rs_staf_odpoved_tabulka" border="0" cellpadding="2" cellspacing="1">';
        foreach ($pole as $sl) {
             vypis_polozky ($sl[1],$zaznam[$sl[0]]);
        }
        echo '</table>';
        echo '</fieldset>';
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($je_to_dotaz_pom ['dotaz'] != "") {
    echo '<br><a href="?list=prehled_dotazu&pozice='.$pozice.'&app=staffer">Zpět na seznam dotazů</a>';
}
else {
    echo '<br><a href="?list=prehled_prihlasek&pozice='.$pozice.'&app=staffer">Zpět na seznam přihlášek</a>';
}

echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <a href="?list=smazat_odpoved&pozice='.$pozice.'&odpoved='.$odpoved.'&app=staffer">
      <img src="app/staffer/img/smazat.png"> Smazat</a><br>';

MySQL_Query("UPDATE staffer_odpovedi SET precteno='1' WHERE id_odpoved='$odpoved'");
MySQL_CLOSE ($connect);
?>
