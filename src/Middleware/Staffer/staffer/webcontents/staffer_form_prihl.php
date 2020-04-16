<?php
use Middleware\Staffer\AppContext;

//zpracovani prom pozice
if (isset($_GET['pozice'])) {
     $pozice = $_GET['pozice'];
}
else {
     if (isset($_POST['pozice'])) {$pozice = $_POST['pozice'];}
     else {
       echo '<p class="chyba">Soubor s životopisem byl příliš velký!</p>';
       $krok = 1;
       //$pozice = '123456789012345678';
    }
}


//if (strlen($pozice) != 18) {$pozice='';}
$pozice = strip_tags ($pozice);
$pozice = addslashes($pozice);

$handler = AppContext::getDb();

if ($pozice != '') {
  //zobrazeni prislusneho formulare k pozici
  $vysledek = MySQL_Query("select typ,nazev from staffer_pozice where id='$pozice'");
  if (mysql_num_rows($vysledek)) {
    $zaznam_r = MySQL_Fetch_Array($vysledek);
    $form = $zaznam_r['typ'];
    $nazev = $zaznam_r ['nazev'];


    if (isset($_GET['krok'])) {$krok = $_GET['krok'];}
    else {$krok = '1';}
    include_once 'rs/app/staffer/def_pole.php';


//obsah formulare
    $add = 'rs/app/staffer/forms/'.$form.'.php';
    //echo '<br> . *ve STAFFER_FORM.PHP**add* '  . $add;


    if (!file_exists($add)) {echo 'Pro tuto pozici není připraven kontaktní formulář.';}
    else {
      if ($krok != 3){
                echo '<H2>Přihláška na pozici: '.$nazev.'</H2>';
                if ($krok == 2) {echo '<H5>Kontrola údajů</H5>';}

                if (count($_POST)==0 AND count($_GET)==0) {echo '<p class="chyba">Soubor s životopisem byl příliš velký!</p>';}

                                      //hlavicka formulare VPRED
                $dalsi_krok = $krok + 1;


                echo '<form method="POST" enctype="multipart/form-data" action="?list=' .
                            konstStaffer_prihlasovaci_formular . '&form='.$form.'&krok='.$dalsi_krok.'&pozice='.$pozice.'">';

                                      echo '<br><input type="hidden" value="'.$pozice.'" name="pozice">';

                                      include_once 'rs/app/staffer/pole.php';
                                      include_once $add;

                                      //odeslani formulare
                                      $ok = 1;
                                      foreach ($pole as $klic=>$hodnoty)  {
                                                    $funkce='$vysl = '.$hodnoty[3].' ($klic,$hodnoty,0);';
                                                    eval ($funkce);
                                                    IF ($vysl[2] == 0) {
                                                        $ok=0;}
                                      }
                                      //echo '<br><input type="hidden" value="'.$pozice.'" name="pozice">';

                                      if ($ok OR $krok == 1){
                                          echo '<input type="submit" value="ODESLAT PŘIHLÁŠKU" name="save">';}
                                      echo '</form>';

                                      if ($krok!=1){
                                                    include 'rs/app/staffer/zpet.php';
                                      }
      } //$krok != 3

      if ($krok == 3) {
                 include 'pole.php';
                 include $add;
                 include 'krok3.php';
      }
    }

  }
  ELSE
  {echo '<p class="chyba">Požadovaná pozice neexistuje!</p>';
  }

}
else {
//vypis hledanych pozic, uverejnenych
//include 'contents/staffer_list.php';
//include 'rs/app/staffer/webcontents/' . konstStaffer_seznam_pozic . '.php';


}
?>
