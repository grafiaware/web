<?php
//include '../../data.inc.php';
use Middleware\Staffer\AppContext;
$handler = AppContext::getDb();

//$vysledek = MySQL_Query("SELECT id,nazev,pozice_s_odmenou, aktiv,aktiv_start,aktiv_stop,kontakt_id FROM staffer_pozice ORDER BY nazev");
$statement = $handler->query("SELECT id,nazev,pozice_s_odmenou, aktiv,aktiv_start,aktiv_stop,kontakt_id FROM staffer_pozice ORDER BY nazev");
$statement->execute();

echo '<H3>Přehled všech pozic</H3>';
//if (!mysql_num_rows($vysledek)) {echo '<p>V tuto chvíli není vypsána žádná volná pracovní pozice.</p>';}
if (!$statement->rowCount()) {echo '<p>V tuto chvíli není vypsána žádná volná pracovní pozice.</p>';}
foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $zaznam) {
//WHILE ($zaznam = MySQL_Fetch_Array($vysledek)) {
            echo '<fieldset><legend><b>'.$zaznam['nazev'].'</b></legend>';
            echo '<DIV id="rs_staf_prehled_tl_l">';
            if ($zaznam['aktiv'] == 0) {echo '<span id="rs_staf_neverejna"><img src="'.AppContext::getAppPublicDirectory().'backgr/cervena.gif"><br>Neveřejná</span>';}
            if ($zaznam['aktiv'] == 1) {echo '<span id="rs_staf_verejna"><img src="'.AppContext::getAppPublicDirectory().'backgr/zelena.gif"><br>Veřejná</span>';}
            if ($zaznam['aktiv'] == 2) {
                                        IF (strtotime($zaznam['aktiv_start']) <= time() AND time() <= strtotime($zaznam['aktiv_stop']." + 23 hours 59 minutes 59 seconds"))
                                             {echo '<span id="rs_staf_verejna"><img src="'.AppContext::getAppPublicDirectory().'backgr/zelena.gif"><br>Veřejná</span>';}
                                        ELSE {echo '<span id="rs_staf_neverejna"><img src="'.AppContext::getAppPublicDirectory().'backgr/cervena.gif"><br>Neveřejná</span>';}
                                        }
            echo '</DIV>';

            echo '<DIV id="rs_staf_prehled_tl_l">';
            echo '<a href="?list=uprava_pozice&pozice='.$zaznam['id'].'&app=staffer"><img src="'.AppContext::getAppPublicDirectory().'img/upravit.png"><br>Upravit</a>';
            echo '</DIV>';

            echo '<DIV id="rs_staf_prehled_tl_l">';
            echo '<a href="?list=prehled_prihlasek&pozice='.$zaznam['id'].'&app=staffer"><img src="'.AppContext::getAppPublicDirectory().'img/odpoved.png"><br>Přihlášky</a><br>';

            echo '<a href="?list=prehled_dotazu&pozice='.$zaznam['id'].'&app=staffer"><img src="'.AppContext::getAppPublicDirectory().'img/odpoved.png"><br>Dotazy</a>';
            echo '</DIV>';
            //----------------------------------------------

//            $vysledek2 = MySQL_Query("SELECT id_odpoved FROM staffer_odpovedi WHERE precteno ='0' AND id_pozice = '$zaznam[id]' and dotaz='' "); //prihlasky neprectene
//            $pocet_prihlasek_neprectenych = mysql_num_rows ($vysledek2);
            $statement2 = $handler->query("SELECT id_odpoved FROM staffer_odpovedi WHERE precteno ='0' AND id_pozice = '$zaznam[id]' and dotaz='' "); //prihlasky neprectene
            $statement2->execute();
            $pocet_prihlasek_neprectenych = $statement2->rowCount();

//            $vysledek2 = MySQL_Query("SELECT id_odpoved FROM staffer_odpovedi WHERE precteno ='0' AND id_pozice = '$zaznam[id]' and dotaz!='' "); //dotazy neprectene
//            $pocet_dotazu_neprectenych = mysql_num_rows ($vysledek2);
            $statement2 = $handler->query("SELECT id_odpoved FROM staffer_odpovedi WHERE precteno ='0' AND id_pozice = '$zaznam[id]' and dotaz!='' "); //dotazy neprectene
            $statement2->execute();
            $pocet_dotazu_neprectenych = $statement2->rowCount();

            echo '<DIV id="rs_staf_prehled">';
            $odp = 'Nepřečtené přihlášky: '.$pocet_prihlasek_neprectenych;
            if ($pocet_prihlasek_neprectenych > 0) {
                $odp = ' <a href="?list=prehled_prihlasek&pozice='.$zaznam['id'].'&app=staffer">'.$odp.'</a>';
            }
            echo $odp;

            $odp = '<br><br>Nepřečtené dotazy: '.$pocet_dotazu_neprectenych ;
            if ($pocet_dotazu_neprectenych > 0) {
                $odp = ' <a href="?list=prehled_dotazu&pozice='.$zaznam['id'].'&app=staffer">'.$odp.'</a>';
            }
            echo $odp;
            echo '</DIV>';

            //----------------------------------
            echo '<DIV id="rs_staf_prehled">';
//            $vysledek2 = MySQL_Query("SELECT id_odpoved FROM staffer_odpovedi WHERE id_pozice = '$zaznam[id]'  and dotaz=''  ");  //prihlasky celkem
//            $pocet_prihlasek = mysql_num_rows ($vysledek2);
            $statement2 = $handler->query("SELECT id_odpoved FROM staffer_odpovedi WHERE id_pozice = '$zaznam[id]'  and dotaz=''  ");  //prihlasky celkem
            $statement2->execute();
            $pocet_prihlasek = $statement2->rowCount();
            echo '<span>Počet došlých prihlášek: '.$pocet_prihlasek.'</span>';

//            $vysledek2 = MySQL_Query("SELECT id_odpoved FROM staffer_odpovedi WHERE id_pozice = '$zaznam[id]'  and dotaz!=''  ");  //dotazy celkem
//            $pocet_dotazu = mysql_num_rows ($vysledek2);
            $statement2 = $handler->query("SELECT id_odpoved FROM staffer_odpovedi WHERE id_pozice = '$zaznam[id]'  and dotaz!=''  ");  //dotazy celkem
            $statement2->execute();
            $pocet_dotazu = $statement2->rowCount();
            echo '<br><br><span>Počet došlých dotazů: '.$pocet_dotazu.'</span>';
            echo '</DIV>';

            //-------------------------------------
            echo '<DIV id="rs_staf_prehled">';
            echo '<img src="'.AppContext::getAppPublicDirectory().'img/osoba.png"> <span>Kontaktní&nbsp;osoba: <br></span>';
//            $vysledek2 = MySQL_Query("SELECT jmeno,prijmeni FROM staffer_kontos WHERE id = '$zaznam[kontakt_id]'");
//            $zaznam2 = MySQL_Fetch_Array($vysledek2);
            $statement2 = $handler->query("SELECT jmeno,prijmeni FROM staffer_kontos WHERE id = '$zaznam[kontakt_id]'");
            $statement2->execute();
            $zaznam2 = $statement2->fetch(PDO::FETCH_ASSOC);

//            if (mysql_num_rows ($vysledek2) > 0) {
            if ($zaznam2) {
                echo '<span>' . $zaznam2['jmeno'].' '.$zaznam2['prijmeni']. '</span>';
            } else {
                echo '<span class="chyba">Pozor! Chybí kontaktní osoba.</span>';
            }
            echo '</DIV>';

    if ($zaznam['pozice_s_odmenou']) {
        echo '<DIV id="rs_staf_prehled">';
        echo '<img src="'.AppContext::getAppPublicDirectory().'img/ikoclo16.gif" title="pozice_s_odmenou"><span><br>Pozice s&nbsp;odměnou</span>';
        echo '</DIV>';
    }
                                                echo '<DIV id="rs_staf_prehled_tl_r" >';
                                                echo '<a href="?list=smazat_pozici&pozice='.$zaznam['id'].'&app=staffer"><img src="'.AppContext::getAppPublicDirectory().'img/smazat.png"><br>Zrušit</a>';
                                                echo '</DIV>';
                                                echo '</fieldset><br>';
}
//MySQL_CLOSE ($connect);



?>
<!-- <INPUT type="button" name="publikace_volnych_pozic"  value="Publikovat tabulku volnych pozic)">
<a href="?list=nova_kontos&app=staffer" class="polozkaon">Založit novou kontaktní osobu</a>
<a style=" display: block" href="?list=nova_kontos&app=staffer">Založit novou kontaktní osobu</a> -->
