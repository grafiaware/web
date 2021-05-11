<?php
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform.inc";
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_cselect.inc";
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_cselect_multi.inc";
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_htmlarea.inc";
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_input_checkbox.inc";
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_input_text.inc";
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_radio.inc";
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_textarea.inc";

require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/obecna_knih.inc";


/**
 * Objekt 'webovy formular - seznam planovanych kurzu'
 *
 * vcetne tlacitek Upravit, Smazat,..duplikovat - u kazdeho kurzu a tl. Pridat novy kurz - pod seznamem
 *
 * Vystup - POST promenne 'action' a 'item_id'
 */

class f_planovane_kurzy_seznam  extends webform {
    private $error_bool=false;
    private $mdb2;
    private $action_script;

    public function __construct($name,$action_script) {
        $this->mdb2 = Middleware\Edun\AppContext::getDb();
        //print_r($this->mdb2);                // *SEL*
        $this->action_script=$action_script;

        parent::__construct($name,$action_script);
    }

    public function Display($por=false) {
           switch ($por) {
       case "kod":
           $poradi="kod_kurzu";   break;
       case "nazev":
           $poradi="nazev_kurzu";  break;
       case "oddata":
           $poradi="od_data_plan";  break;
       case "por_cislo":
           $poradi="kategorie_kurzu_kod, kurz_poradove_cislo";  break;
       default:
           $poradi="kod_kurzu";
       }

        //echo '<a href="index.php?'.'order=kod'.'&list=katalog_kurzu&app=edun" class=polozkaon>razeni</a>';

        $sql_query = 'SELECT  id_planovane_kurzy, nazev_kurzu, kod_kurzu,
                       kurz_poradove_cislo, kategorie_kurzu_kod,
                       od_data_plan, do_data_plan, cena_plan,
                       misto_obec, misto_ulice, misto_cislo, misto_kurzu_deleted,
                       id_kurz, inserted, updated, kurz_deleted
                     FROM view_planovane_kurzy_prehled ORDER BY ' . $poradi;

            //echo '<br>';  print_r($sql_query);     /*SEL*/
        $res = $this->mdb2->query($sql_query);
        parent::Display('top');

//        echo '<br>*formularove pole action*(k_k_obj)hidden:';
//        echo '<input type="text" name="action" id="action" readonly>'."\n";
//        echo "<br>";
//        echo '*formularove pole item_id*(k_k_obj)hidden:';
//        echo '<input type="text" name="item_id" id="item_id" readonly>'."\n";
//        echo "<br>";
        echo '<input type="hidden" name="action" id="action">'."\n";
        echo '<input type="hidden" name="item_id" id="item_id">'."\n";

        echo '<h3>Plánované kurzy</h3>';
        if ($res) {
            echo '<table '.webform_styles::$kurzy_table.'>'."\n";
            //rozvrzeni sloupcu a hlavicka tabulky
            echo '<tr>'."\n";
            echo '<th '.webform_styles::$plan_kurzy_table_spacing['before'].'>&nbsp</th>'."\n";

            //echo '<th>&nbsp</th>'."\n";
            echo '<th colspan=2'.webform_styles::$plan_kurzy_table_spacing['kurz_poradove_cislo'].
                 '><a href="index.php?'.'order=por_cislo'.'&list=planovane_kurzy&app=edun"' .  webform_styles::$podtrzeni  .' >Pořadové číslo</a></th>'.
                 "\n";
            echo '<th '.webform_styles::$plan_kurzy_table_spacing['kod_kurzu'].
                 '><a href="index.php?'.'order=kod'.'&list=planovane_kurzy&app=edun"' .  webform_styles::$podtrzeni  .' >Kód kurzu</a></th>'.
                 "\n";
            echo '<th '.webform_styles::$plan_kurzy_table_spacing['nazev_kurzu'].
                 '><a href="index.php?'.'order=nazev'.'&list=planovane_kurzy&app=edun"' .  webform_styles::$podtrzeni  . ' >Název kurzu</a></th>'."\n";
            echo '<th '.webform_styles::$plan_kurzy_table_spacing['misto_obec'].'>Místo kurzu</th>'."\n";
            echo '<th '.webform_styles::$plan_kurzy_table_spacing['cena_plan'].'>Cena kurzu</th>'."\n";
            echo '<th '.webform_styles::$plan_kurzy_table_spacing['datum_kurzu'].
                 '><a href="index.php?'.'order=oddata'.'&list=planovane_kurzy&app=edun"'.  webform_styles::$podtrzeni  . ' >Plánované datum kurzu</th>'."\n";
            //echo '<th '.webform_styles::$plan_kurzy_table_spacing['druh_kurzu'].
            //     '><a href="index.php?'.'order=druh'.'&list=katalog_kurzu&app=edun"' . .  webform_styles::$podtrzeni  . ' >Druh kurzu</a></th>'."\n";
            echo '<th '.webform_styles::$plan_kurzy_table_spacing['buttons'].'>&nbsp</th>'."\n";
            //echo '<th '.webform_styles::$plan_kurzy_table_spacing['over'].'>&nbsp</th>'."\n";
            echo '</tr>'."\n";

            //data=kurzy
            while ($kurz=$res->fetch(\PDO::FETCH_ASSOC)) {
                if($kurz['updated']) {
                    $sql_date = $kurz['updated'];
                }
                else {
                    $sql_date = $kurz['inserted'];
                }
                echo '<tr>'."\n";
                echo '<td>&nbsp;'; if (($kurz['kurz_deleted'])) {echo '!!-';}
                echo '</td>'."\n";

                echo '<td '. webform_styles::$plan_kurzy_table_td_doprava . '>'.$kurz['kategorie_kurzu_kod'].'&nbsp</td>'."\n";
                echo '<td '. webform_styles::$plan_kurzy_table_td_doprava . '>'.$kurz['kurz_poradove_cislo'].'&nbsp</td>'."\n";
                echo '<td '. webform_styles::$plan_kurzy_table_td_doprava . '>'.$kurz['kod_kurzu'].'&nbsp&nbsp</td>'."\n";
                //echo '<td>'.$kurz['nazev_kurzu'].'('.id_code::code($kurz['id_kurz'],$sql_date).' id='.id_code::decode(id_code::code($kurz['id_kurz'],$sql_date)).')</td>'."\n";
                echo '<td>'.$kurz['nazev_kurzu'].'&nbsp&nbsp</td>'."\n";
                echo '<td>';
                  if ($kurz['misto_kurzu_deleted']) {
                    echo '!!- ';
                  }
                echo $kurz['misto_obec'].', ' .$kurz['misto_ulice'].' '.$kurz['misto_cislo'].'</td>'."\n";
                if  ($kurz['cena_plan']) {echo '<td '.
                          webform_styles::$plan_kurzy_table_td_doprava . '>'.$kurz['cena_plan'].' Kč&nbsp&nbsp</td>'."\n"; }
                else {echo '<td>&nbsp</td>'."\n"; }
                echo '<td>'. Obecna_Knih::datum_ymd_dmy2($kurz['od_data_plan']) . ' - '.
                             Obecna_Knih::datum_ymd_dmy2($kurz['do_data_plan']) .'</td>'."\n";

                echo '<td><a href="javascript: submit_edit(\''.id_code::code($kurz['id_planovane_kurzy'],$sql_date).'\') "> <img src="img/b_edit.png" alt="Upravit plánování kurzu" title="Upravit plánování kurzu" border="0"></a>
                          <a href="javascript: submit_drop(\''.id_code::code($kurz['id_planovane_kurzy'],$sql_date).'\') "> <img src="img/b_drop.png" alt="Odstranit naplánovaný kurz ze seznamu" title="Odstranit naplánovaný kurz ze seznamu" border="0"></a>
                          <a href="javascript: submit_dupl(\''.id_code::code($kurz['id_planovane_kurzy'],$sql_date).'\') "> <img src="img/b_copy.png" alt="Duplikovat plánovaný kurz" title="Duplikovat plánovaný kurz" border="0"></a>
                      </td>'."\n";
               // echo '<td>&nbsp</td>'."\n";
                echo '</tr>'."\n";

                //print_r($kurz);
            }
            echo '</table>';
            echo '<span '. webform_styles::$drobna_poznamka .'>&nbsp;&nbsp;!!- &nbsp;&nbsp; označuje, že kurz, popř. zvolená položka je znepřístupněn/a</span><br>';

        }
        echo '<br><INPUT type="button" name="new_kurz" onClick="submit_new()" value="Naplánovat nový kurz">';
        if (isset($_SESSION ['sess_prava']['role']) AND ($_SESSION ['sess_prava']['role'] == "sup" or $_SESSION ['sess_prava']['role'] == "adm")) {
          echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .
               '<INPUT type="button" name="publikace" onClick="submit_publ_planovane()" value="Publikovat tabulku (harmonogram)">';
               //.' na stránku:' . '<INPUT type="input" name="publ_stranka" id="publ_stranka">';
        }
        parent::Display('bottom');
    }




    public function Publikuj($por=false) {
           switch ($por) {
       case "kod":
           $poradi="kod_kurzu";   break;
       case "nazev":
           $poradi="nazev_kurzu";  break;
       case "oddata":
           $poradi="od_data_plan";  break;
       case "por_cislo":
           $poradi="kategorie_kurzu_kod, kurz_poradove_cislo";  break;
       default:
           $poradi="kod_kurzu";
       }

       //echo '<a href="index.php?'.'order=kod'.'&list=katalog_kurzu&app=edun" class=polozkaon>razeni</a>';

        //hledam, zda jiz existuje list s nazvem HARMONOGRAM
        $sql_query = "SELECT * from stranky WHERE list = 'HARMONOGRAM'";
           // echo "<BR>";    print_r( $sql_query); /*SEL*/
        $res=$this->mdb2->query($sql_query); //!!!pak pouzit numRows pro detekci poctu radek
                            //$res=$this->mdb2->exec($sql_query); //pocet radek je v res
        $nn=$res->rowCount();

        if (!$nn) { // stranka HARMONOGRAM neexistuje

            //TODO: dočasně zablokováno!
            assert(FALSE, "dočasně zablokováno!");

             //aut_gen = 1 pro publikaci harmonogramu
             $sql_query = "INSERT INTO stranky (list,nazev_lan1,aktiv_lan1,nazev_lan2,aktiv_lan2,nazev_lan3,aktiv_lan3,aut_gen,editor)
                             VALUES ('HARMONOGRAM','Harmonogram kurzů','0','Harmonogram kurzů','0','Harmonogram kurzů','0','1','skriptRS')";
                 //echo '<br>';  print_r($sql_query);     /*SEL*/
             $res=$this->mdb2->exec($sql_query);
             //exit;
             //pridat opravneni
             $sql_query='ALTER TABLE opravneni ADD ' .
                       'HARMONOGRAM' .
                       ' TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT \'0\' ';
            //echo '<br>'; print_r($sql_query);
            //exit;
            $res3=$this->mdb2->exec($sql_query);
            $sql_query = 'UPDATE opravneni SET ' .
                       'HARMONOGRAM'  .
                       ' = \'1\' WHERE (role = \'adm\' or  role = \'sup\')';
            //echo '<br>'; print_r($sql_query);
            $res3=$this->mdb2->exec($sql_query);
            $user = $_SESSION ["sess_user"];
            $vysledek = MySQL_Query("select * from opravneni where user='$user'");
            $zaznam_opravneni = MySQL_Fetch_Array($vysledek);
            $_SESSION ["sess_prava"] = $zaznam_opravneni;



        }


        //generovani obsahu  stranky HARMONOGRAM

          $sql_query = 'SELECT  id_planovane_kurzy, nazev_kurzu, kod_kurzu,
                       kurz_poradove_cislo, kategorie_kurzu_kod, id_kurz,
                       od_data_plan, do_data_plan, cena_plan,
                       misto_obec, misto_ulice, misto_cislo,
                       id_kurz, inserted, updated, kurz_deleted
                     FROM view_planovane_kurzy_prehled ORDER BY ' . $poradi;
                 //echo '<br>';  print_r($sql_query);     /*SEL*/
          $res = $this->mdb2->query($sql_query);

          $str = "";
          // $str .= '<br><h3>Harmonogram kurzů</h3>';
          $str .= '<br><table ' .
            webform_styles::$kurzy_table_web . '>'.
            //rozvrzeni sloupcu a hlavicka tabulky
            '<tr>' .
            //'<th' . webform_styles::$kurzy_table_web_spacing['kod_kurzu']   .'>Kód kurzu</th>'.
            '<th' . webform_styles::$kurzy_table_web_spacing['nazev_kurzu'] .'>Název kurzu</th>'.
            '<th' . webform_styles::$kurzy_table_web_spacing['misto_obec']  .'>Místo kurzu</th>'.
            '<th' . webform_styles::$kurzy_table_web_spacing['datum_kurzu'] .'>Plánované datum<br>kurzu</th>' .
            '<th' . webform_styles::$kurzy_table_web_spacing['cena_plan'] .'>Cena<br>kurzu</th>' .
            '<th' . webform_styles::$kurzy_table_web_spacing['podrobnosti'] .'>Detail <br>kurzu</th>' .
            '<th' . webform_styles::$kurzy_table_web_spacing['prihlasit'] .'>Chci se<br>přihlásit</th>' .
            '</tr>' ;

            //data=kurzy
          while ($kurz=$res->fetch(\PDO::FETCH_ASSOC)) {

           $str .=
             '<tr>'.
             //'<td'. webform_styles::$kurzy_table_td_web . '>'. $kurz['kod_kurzu']. '&nbsp;</td>'.
             '<td'. webform_styles::$kurzy_table_td_web . '>'.$kurz['nazev_kurzu']. '&nbsp;</td>'.
             '<td'. webform_styles::$kurzy_table_td_web . '>' . $kurz['misto_obec']. ', ' .
             $kurz['misto_ulice']. ' '. $kurz['misto_cislo']. '&nbsp;</td>'.
             '<td'. webform_styles::$kurzy_table_td_web . '>'.
                     Obecna_Knih::datum_ymd_dmy2($kurz['od_data_plan']) . ' - '.
                     Obecna_Knih::datum_ymd_dmy2($kurz['do_data_plan']) . '&nbsp;</td>';
             if ($kurz['cena_plan']) {
               $str .= '<td'. webform_styles::$kurzy_table_td_web_doprava . '>'.$kurz['cena_plan']. '&nbsp;Kč&nbsp;</td>';}
             else { $str .= '<td' .webform_styles::$kurzy_table_td_web_doprava  . '>&nbsp;</td>';}

             $str .=
             '<td'. webform_styles::$kurzy_table_td_web.'>'.
                  '<a href="index.php?list=v0&ci='.  $kurz['id_kurz'] .    //$kurz['kod_kurzu'] .
                  /*'&rlist=' . $nalezeny_list .*/
                  '">'.
                  'ukázat...&nbsp;</a></td>'.
             '<td'. webform_styles::$kurzy_table_td_web.'>'.
                  '<a href="index.php?list=f0&ci='. $kurz['id_kurz'] .
                  //'&rlist=' . $nalezeny_list .

                   '&idplan=' . $kurz['id_planovane_kurzy'] .

                 // '&datum=' .  Obecna_Knih::datum_ymd_dmy2($kurz['od_data_plan']) . ' - '.
                 //              Obecna_Knih::datum_ymd_dmy2 ($kurz['do_data_plan']) .
                 // '&misto=' . $kurz['misto_obec'] . ', ' . $kurz['misto_ulice'] . ' ' . $kurz['misto_cislo'] .
                 // '&cena=' . $kurz['cena_plan'] .

                  '">'.
                  'chci se <br>přihlásit...&nbsp;</a></td>'.
             '</tr>';
          }

          $str .= '</table><br>';
          $str .= '<p>Pozn.: cena v Kč bez DPH / osobu<br><br><br></p>';
          //$str .= '<p><span style="font-size: xx-small">Tato stránka se momentálně upravuje, děkujeme za pochopení.</span></p>';


          //zapis obsah_lan1, nazev_lan1, zmena
          $sql_query = 'UPDATE stranky  SET
                    obsah_lan1 = \'' .
                   $str .
                   ' \', nazev_lan1=\'Harmonogram kurzů\', ' .
                   ' zmena=now()' .
                   " WHERE list ='HARMONOGRAM'";
          //echo "<BR>";  echo htmlspecialchars($sql_query,ENT_QUOTES);
          //echo "<BR>print<BR> "; print_r( $sql_query);

          $res=$this->mdb2->exec($sql_query);

        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">';
        echo '<html>';
        echo '<head>';
        echo '<meta http-equiv="refresh" content="5;url='.$this->action_script.'">';
        echo '<meta http-equiv="Content-Type" CONTENT="text/html; charset=utf-8">';
        echo '<meta http-equiv="Content-Language" content="cs">';
        echo '<title>Změny uloženy</title>';
        echo '</head>';
        echo '<body>';
        echo '<h3>Tabulka plánovaných kurzů HARMONOGRAM byla přepsána.</h3>';
        echo '<p>... vyčkejte, prosím </p><br><br>';
        echo '</body>';
        echo '</html>';


    } //function Publikuj





} //class f_planovane_kurzy_seznam





?>