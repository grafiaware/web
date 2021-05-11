<?php
/**
 * Object depository for katalog_kurzu.php
 *
 * @name katalog_kurzu_seznam.inc
 *
 * @version 1.0
 * @package webform_obj
 */

require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform.inc";
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_cselect.inc";
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_cselect_multi.inc";
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_htmlarea.inc";
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_input_checkbox.inc";
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_input_text.inc";
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_radio.inc";
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_textarea.inc";

/****************************** fce **********************************************************/


/****************************************************************************************/


/**
 * Objekt 'webovy formular - seznam vsech kurzu'
 *
 * vcetne tlacitek Upravit, Přesunout do archivu, Duplikovat  - u kazdeho kurzu a tl. Pridat novy kurz - pod seznamem
 *
 * Vystup - POST promenne 'action' a 'item_id'
 */

class  f_katalog_kurzu_seznam extends webform {
    private $error_bool=false;
    private $mdb2;

    private $action_script;

    public function __construct($name,$action_script) {
        $this->mdb2 = Middleware\Edun\AppContext::getDb();
        $this->action_script=$action_script;

        parent::__construct($name,$action_script);
    }

    public function Display($por=false) {
           switch ($por) {
       case "kod":
           $poradi="kod_kurzu";   break;
       case "nazev":
           $poradi="nazev_kurzu";  break;
       case "druh":
           $poradi="druh_kurzu,kod_kurzu";  break;
       case "kategorie":
           $poradi="kategorie_kurzu, nazev_kurzu";  break;
       case "por_cislo":
           $poradi="kategorie_kurzu_kod, kurz_poradove_cislo";  break;

       case "pocet_casti":
           $poradi="pocet_casti DESC";  break;
       case "pocet_modulu":
           $poradi="pocet_modulu DESC";  break;
        case "delka_obsahu_skript_kurzu":
           $poradi="delka_obsahu_skript_kurzu DESC";  break;

       default:
           $poradi="kod_kurzu";
       }

        //echo '<a href="index.php?'.'order=kod'.'&list=katalog_kurzu&app=edun" class=polozkaon>razeni</a>';

        $sql_query = 'SELECT id_kurz, nazev_kurzu, kod_kurzu,
                      druh_kurzu, druh_kurzu_deleted,
                      kurz_poradove_cislo, kategorie_kurzu_kod,
                      kategorie_kurzu, kategorie_kurzu_deleted, publikovan,
                      inserted, updated
                      FROM view_kurzy_prehled ORDER BY ' . $poradi;
        $sql_query = '   SELECT

                (select
                    count(vzb_kurz_cast_kurzu.id_cast_kurzu_FK)
                    from vzb_kurz_cast_kurzu
                    where  view_kurzy_prehled.id_kurz = vzb_kurz_cast_kurzu.id_kurz_FK
                    group by vzb_kurz_cast_kurzu.id_kurz_FK)   as pocet_casti,

                (select
                    count(vzb_kurz_cast_kurzu.id_cast_kurzu_FK)
                    from vzb_kurz_cast_kurzu
                    left join vzb_cast_modul_kurzu on  vzb_cast_modul_kurzu.id_cast_kurzu_FK = vzb_kurz_cast_kurzu.id_cast_kurzu_FK

                    where  view_kurzy_prehled.id_kurz = vzb_kurz_cast_kurzu.id_kurz_FK
                    group by vzb_kurz_cast_kurzu.id_kurz_FK)   as pocet_modulu,

                sum( if( isnull( char_length(modul_obsah_skripta)  ),0,char_length(modul_obsah_skripta) ) ) as delka_obsahu_skript_kurzu,

                        view_kurzy_prehled .id_kurz,  view_kurzy_prehled .nazev_kurzu, view_kurzy_prehled .kod_kurzu,
                        druh_kurzu, druh_kurzu_deleted,
                        kurz_poradove_cislo, kategorie_kurzu_kod,
                        kategorie_kurzu, kategorie_kurzu_deleted, publikovan,
                        view_kurzy_prehled.inserted,  view_kurzy_prehled.updated

                        FROM view_kurzy_prehled

                left join vzb_kurz_cast_kurzu on    vzb_kurz_cast_kurzu.id_kurz_FK               = view_kurzy_prehled.id_kurz
                left join vzb_cast_modul_kurzu on  vzb_cast_modul_kurzu.id_cast_kurzu_FK = vzb_kurz_cast_kurzu.id_cast_kurzu_FK
                left join modul_kurzu on                   modul_kurzu.id_modul_kurzu  =  vzb_cast_modul_kurzu.id_modul_kurzu_FK
                group by view_kurzy_prehled.id_kurz
                        ORDER BY ' . $poradi;


       // print_r($sql_query);                // *SEL*
        $res = $this->mdb2->query($sql_query);
        parent::Display('top');

//        echo '*formularove pole action*hidden:';
//        echo '<input type="text" name="action" id="action" readonly>'."\n";
//        echo "<br>";
//        echo '*formularove pole item_id*hidden:';
//        echo '<input type="text" name="item_id" id="item_id" readonly>'."\n";
//        echo "<br>";
        echo '<input type="hidden" name="action" id="action">'."\n";
        echo '<input type="hidden" name="item_id" id="item_id">'."\n";

        echo '<h3>Katalog</h3>';
            echo '<INPUT type="button" name="new_kurz" onClick="submit_new()" value="Přidat nový kurz">';

            echo '<table '.webform_styles::$kurzy_table.'>'."\n";
            //rozvrzeni sloupcu a hlavicka tabulky
            echo '<tr>'."\n";
            echo '<th '.webform_styles::$kurzy_table_spacing['before'].
                 '>Publi kován</th>'."\n";


          echo '<th colspan=2'.webform_styles::$kurzy_table_spacing['kurz_poradove_cislo'].
                 '><a href="index.php?'.'order=por_cislo'.'&list=katalog_kurzu&app=edun"' . webform_styles::$podtrzeni .  '>Pořadové číslo</a></th>'.
                 "\n";
         // echo '<th >&nbsp</th>'. "\n";


            echo '<th '.webform_styles::$kurzy_table_spacing['kod_kurzu'].
                 '><a href="index.php?'.'order=kod'.'&list=katalog_kurzu&app=edun"' . webform_styles::$podtrzeni .  '>Kód kurzu</a></th>'.
                 "\n";
            echo '<th '.webform_styles::$kurzy_table_spacing['nazev_kurzu'].
                 '><a href="index.php?'.'order=nazev'.'&list=katalog_kurzu&app=edun"' . webform_styles::$podtrzeni .  '>Název kurzu</a></th>'."\n";
            echo '<th '.webform_styles::$kurzy_table_spacing['druh_kurzu'].
                 '><a href="index.php?'.'order=druh'.'&list=katalog_kurzu&app=edun"' . webform_styles::$podtrzeni .  '>Druh kurzu</a></th>'."\n";
            echo '<th '.webform_styles::$kurzy_table_spacing['kategorie_kurzu'].
                 '><a href="index.php?'.'order=kategorie'.'&list=katalog_kurzu&app=edun"' . webform_styles::$podtrzeni .  '>Kategorie kurzu</a></th>'."\n";

           echo '<th '.webform_styles::$kurzy_table_spacing['over'].
                 '><a href="index.php?'.'order=pocet_casti'.'&list=katalog_kurzu&app=edun"' . webform_styles::$podtrzeni .  '>počet částí</a></th>'."\n";
           echo '<th '.webform_styles::$kurzy_table_spacing['over'].
                 '><a href="index.php?'.'order=pocet_modulu'.'&list=katalog_kurzu&app=edun"' . webform_styles::$podtrzeni .  '>počet modulů</a></th>'."\n";
           echo '<th '.webform_styles::$kurzy_table_spacing['over'].
                 '><a href="index.php?'.'order=delka_obsahu_skript_kurzu'.'&list=katalog_kurzu&app=edun"' . webform_styles::$podtrzeni . '>délka obsahu skript</a></th>'."\n";

          echo '<th '.webform_styles::$kurzy_table_spacing['buttons'].'>&nbsp</th>'."\n";
          echo '<th '.webform_styles::$kurzy_table_spacing['over'].'>&nbsp</th>'."\n";
          echo '</tr>'."\n";

            //data=kurzy
 /* W */    while ($kurz=$res->fetch(\PDO::FETCH_ASSOC)) {
                if($kurz['updated']) {
                    $sql_date = $kurz['updated'];
                }
                else {
                    $sql_date = $kurz['inserted'];
                }

                echo '<tr>'."\n";
                echo '<td>&nbsp'.
                '<INPUT  type="checkbox" name="kurz_publikovan_'.$kurz['id_kurz'] . '" ' .
                         'value="'.$kurz['id_kurz'] .'"' ;
                if ($kurz['publikovan']) { echo ' checked';}         //checked
                echo     '></td>'."\n";

           echo '<td ' . webform_styles::$kurzy_table_td_doprava. '>'.
                   $kurz['kategorie_kurzu_kod'].  '&nbsp</td>'."\n";

           echo '<td ' . webform_styles::$kurzy_table_td_doprava. '>'.
                   $kurz['kurz_poradove_cislo'].'&nbsp</td>'."\n";

                echo '<td ' . webform_styles::$kurzy_table_td_doprava. '>'.$kurz['kod_kurzu'].'&nbsp&nbsp</td>'."\n";
                //echo '<td>'.$kurz['nazev_kurzu'].'('.id_code::code($kurz['id_kurz'],$sql_date).' id='.id_code::decode(id_code::code($kurz['id_kurz'],$sql_date)).')</td>'."\n";
                echo '<td>'.$kurz['nazev_kurzu'].'&nbsp&nbsp</td>'."\n";
                echo '<td>';
                  if ($kurz['druh_kurzu_deleted']) echo '!!-';
                  echo $kurz['druh_kurzu'].'&nbsp&nbsp</td>'."\n";
                echo '<td>';
                  if ($kurz['kategorie_kurzu_deleted']) echo '!!-';
                  echo $kurz['kategorie_kurzu'].'&nbsp&nbsp</td>'."\n";

            echo '<td ' .  webform_styles::$kurzy_table_td_doprava. '>' . $kurz['pocet_casti'].'&nbsp&nbsp</td>'."\n";
            echo '<td ' .  webform_styles::$kurzy_table_td_doprava. '>' . $kurz['pocet_modulu'].'&nbsp&nbsp</td>'."\n";
            echo '<td ' .  webform_styles::$kurzy_table_td_doprava. '>&nbsp' . $kurz['delka_obsahu_skript_kurzu'].'&nbsp&nbsp</td>'."\n";

                echo '<td><a href="javascript: submit_edit(\''.id_code::code($kurz['id_kurz'],$sql_date).'\') "><img src="img/b_edit.png" alt="Upravit katalogový kurz" title="Upravit katalogový kurz" border="0"></a>
                          <a href="javascript: submit_drop(\''.id_code::code($kurz['id_kurz'],$sql_date).'\') "><img src="img/b_drop.png" alt="Kurz přesunout z katalogu do archivu" title="Kurz přesunout z katalogu do archivu" border="0"></a>
                          <a href="javascript: submit_dupl(\''.id_code::code($kurz['id_kurz'],$sql_date).'\') "> <img src="img/b_copy.png" alt="Duplikovat katalogový kurz" title="Duplikovat katalogový kurz" border="0"></a>
                          <a href="javascript: submit_skripta(\''.id_code::code($kurz['id_kurz'],$sql_date).'\')"> <img src="img/b_skripta.png" alt="Skripta kurzu" title="Skripta kurzu" border=""></a>
                          <a href="javascript: submit_skripta_download(\''.id_code::code($kurz['id_kurz'],$sql_date).'\')"> <img src="img/b_skripta_down.png" alt="Stahovat skripta kurzu" title="Stahovat skripta kurzu" border=""></a>
                      </td>'."\n";
                echo '<td>&nbsp</td>'."\n";
                echo '</tr>'."\n";

                //print_r($kurz);
            }
            echo '</table>';
            echo '<span '. webform_styles::$drobna_poznamka .'><br>&nbsp;&nbsp;!!- &nbsp;&nbsp; označuje, že zvolená položka je znepřístupněna<br></span>';

        echo '<table><tr><td ' .  webform_styles::$kurzy_table_tlacitka['pridat'] . '>';
        echo '<INPUT type="button" name="new_kurz" onClick="submit_new()" value="Přidat nový kurz">';
        echo '</td>';

//                if (($_SESSION ['sess_prava']['role'] == "sup") or ($_SESSION ['sess_prava']['role'] == "adm"))  {
//TODO: Svoboda - tady vynutá kontrola oprávnění
        if(true) {
          echo '<td>';
          echo '<INPUT type="button" name="publikace" onClick="submit_publ_katalog()" value="Publikovat vybrané katalogové kurzy">';
          echo '</td><td '. webform_styles::$kurzy_table_tlacitka['publikovat']  .' >';
          echo '<span '. webform_styles::$drobna_poznamka .'>Kurzy budou roztříděné podle druhu kurzu a seřazené podle kódu kurzu.<br></span>';
              // . ' na stránku:' . '<INPUT type="input" name="publ_stranka" id="publ_stranka">';
          echo '</td>';
        }
        echo '</tr></table><br>';
        /*
        if ($_SESSION ['sess_prava']['role'] == "adm")  {
             echo '<INPUT type="button" name="poradove_cislo_kurzu" onClick="submit_poradove_cislo_kurzu()" value="Nastavit pořadové číslo kurzů">';
        }*/

        parent::Display('bottom');
    }



     public function Publikuj($por=false) {
            switch ($por) {
       case "kod":
           $poradi="kod_kurzu";   break;
       case "nazev":
           $poradi="nazev_kurzu";  break;
       case "druh":
           $poradi="druh_kurzu,kod_kurzu";  break;
       default:
           $poradi="kod_kurzu";
       }

       //echo '<a href="index.php?'.'order=kod'.'&list=katalog_kurzu&app=edun" class=polozkaon>razeni</a>';

//******************************zapis do sloupce kurz.publikovan, (pro vs.kurzy)*************************
//        //vytvorit pole $akurz_publikovan - to jsou aktualne vybrane na formulari seznamu kurzu
        $akurz_publikovan = array();
        foreach ($_POST as $key => $value) {
            //echo "<br> Klíč: $key; Hodnota: $value<br>\n";
            if (mb_substr($key,0,15,"UTF-8") == 'kurz_publikovan') { //ve value je id kurzu
               // echo ' ulozit ' .$value ;   // *SEL*
                array_push($akurz_publikovan,$value);
            }
        }
        //echo '<br>akurz_publikovan ';  print_r($akurz_publikovan); echo '<br>';

//         // ulozit do tab.kurz,sloupce publikovan - 0/1 (tam kde je treba)
       //vyberu vsechny kurzy
       $sql_query = 'SELECT id_kurz, nazev_kurzu, kod_kurzu, druh_kurzu, kategorie_kurzu, publikovan,
                      kategorie_kurzu_kod, kurz_poradove_cislo
                      inserted, updated
                      FROM view_kurzy_prehled';    // ORDER BY ' . $poradi;
         //echo '<br>'; print_r($sql_query);
       $res = $this->mdb2->query($sql_query);

       //pro vs.kurzy - pokud id je v POStu  tak zapisu 1, (neni-li) ,
       //jinak zapisu 0, (neni-li)
       while ($kurz=$res->fetch(\PDO::FETCH_ASSOC)) {
         //je-li id_kurz v poli akurz_publikovan , tak zapsat 1, jinak 0
         $zapis=false;

         if (in_array($kurz['id_kurz'],$akurz_publikovan)) {   //byl zaskrtnut na formulari
            if (!($kurz['publikovan'])){ //publikovat
                //echo  '<br>' . $kurz['kod_kurzu'] . ':' . ' zapisu  1 do publikovan';
                $zapis=true; $zapisk=1;
            }
         }
         else {   //nebyl zaskrtnut na formulari
            if ($kurz['publikovan']) { //nepublikovat
                //echo  '<br>' . $kurz['kod_kurzu'] . ':' . ' zapisu  0 do publikovan';
                $zapis=true; $zapisk=0;
            }

         }

         if ($zapis===true)  {
           $sql_query = 'UPDATE kurz  SET publikovan = '. $zapisk .
                        ' WHERE id_kurz ='.  $kurz['id_kurz'];
                //echo "<BR>"; print_r($sql_query);

           $res1=$this->mdb2->exec($sql_query);
         }

       }//while



//**********************zapis na stranku urcenou pro katalog (zalozena administratorem v rs, oznacena aut_gen=2)***************************
        $sql_query = "SELECT * from stranky WHERE aut_gen = '2'";    // aut_gen = 2 znaci publikaci katalogu
            //echo "<BR>";    print_r( $sql_query); /*SEL*/


        $res=$this->mdb2->query($sql_query); //query - !!!pak pouzit numRows pro detekci poctu radek
               //$res=$this->mdb2->exec($sql_query); //pocet radek je v res

        //echo '<br>RES '; var_dump($res);
        $nn=$res->rowCount();  //je-li query
                      // $nn=$res->rowCount();

        if ($nn) {        //zadana stranka existuje (alespon jedna)
           if ($nn==1) {      //  stranka je prave jedna

           $vysl=$res->fetch(\PDO::FETCH_ASSOC);
           $nalezeny_list_katalogu = $vysl['list'];
           //echo '<br> list nalezeny :' . $vysl['list']. '<br>';     // *SEL*


           //$pomdelka = mb_strlen(trim($_POST['publ_stranka']) , "UTF-8");
           //echo 'delka nazvu stranky '.$pomdelka;


           //******************************* vymazat substranky ****************

           $sql_query = 'select * from stranky' .
                ' where  (CONVERT( list USING utf8) like "' . $nalezeny_list_katalogu . '_%")';
           $res1 = $this->mdb2->query($sql_query);
           while ($listz = $res1->fetch(\PDO::FETCH_ASSOC) ) {
                   //print_r($listz ['list']);
               //vymazat z tbl. opravneni
               $sql_query2= 'ALTER TABLE opravneni DROP ' . $listz['list'];
               $res2 = $this->mdb2->exec($sql_query2);

               $user = $_SESSION ["sess_user"];

               $sql_query ="select * from opravneni where user='$user'";
               $vysledek = $this->mdb2->query($sql_query);
               $zaznam_opravneni = $vysledek->fetchAll(PDO::FETCH_ASSOC);
               $_SESSION ["sess_prava"] = $zaznam_opravneni;

               //vymazat z tbl. stranky
               $sql_query3 = 'delete from stranky where list=\'' .$listz['list'] . '\'';
               $res3 = $this->mdb2->exec($sql_query3);
           } //while



          /*
          //*******************************stranku naplnit********************
           $sql_query = 'SELECT id_kurz, nazev_kurzu, kod_kurzu, druh_kurzu, kategorie_kurzu, publikovan,
                      inserted, updated
                      FROM view_kurzy_prehled ' .
                      'where publikovan=1 ' .
                      ' ORDER BY kod_kurzu';   //. $poradi;
               //echo '<br>'; print_r($sql_query);      // *SEL*
           $res1 = $this->mdb2->query($sql_query);

           //nactena data=kurzy, priprava obsahu   //
           while ($kurz=$res1->fetch(\PDO::FETCH_ASSOC)) {
           }  //while
   */

          // stranku naplnit zakladnim - nazev, aktivni      //obsah_lan1 = \'' . $str . '\'' .
           $sql_query = 'UPDATE stranky  SET
                    nazev_lan1 =\'Naše katalogové kurzy\'' .
                   ', aktiv_lan1 = \'1\' '.
                   ' WHERE list =\'' . $nalezeny_list_katalogu . '\'';
               //echo "<BR>";  echo htmlspecialchars($sql_query,ENT_QUOTES); // *SEL*
           $res1=$this->mdb2->exec($sql_query);


        //****************************************************************************************************
        //********************************vytvorit substranky a opravneni k nim*******************************
        //***********zjistit kolik druhu ('pouzitych v konkretnich kurzech') je
        $sql_query = 'select druh_kurzu, id_kurz, kod_kurzu,druh_kurzu_kod from view_kurzy_prehled group  by druh_kurzu_kod';
                           // order by druh_kurzu_kod';     //seradi se defaultne podle druhu
              //echo '<br>'; print_r($sql_query);
        $res1 = $this->mdb2->query($sql_query);


        $poledruhu = array();
        //**************pro kazdy druh 'pouzity'************************
        $n_druh=1; //pro cislo listu (stranky)
        while ($druh1=$res1->fetch(\PDO::FETCH_ASSOC)) {      //**** pro kazdy druh****

            //vybrat   kurzy jednoho druhu
            $sql_query = 'SELECT id_kurz, nazev_kurzu, kod_kurzu, druh_kurzu, kategorie_kurzu, publikovan,
                      kategorie_kurzu_kod, kurz_poradove_cislo,
                      inserted, updated
                      FROM view_kurzy_prehled
                      WHERE druh_kurzu = \'' . $druh1['druh_kurzu'] . '\'  and publikovan=1' .
                      ' ORDER BY kod_kurzu'; //. $poradi;
               //echo '<br>'; print_r($sql_query);
            $res2 = $this->mdb2->query($sql_query);

            $seznam_jednoho_druhu="";
            while ($kurz2=$res2->fetch(\PDO::FETCH_ASSOC)) {  //vybere a pripravi kurzy jednoho druhu
                //print_r($kurz2);
              $seznam_jednoho_druhu .= '<a href="index.php?list=v0&ci='. $kurz2['id_kurz'] .
                  '&rlist=' . $nalezeny_list_katalogu .  '_'  .
                            str_pad($n_druh, 2, "0", STR_PAD_LEFT) . '">'.
                  $kurz2['kategorie_kurzu_kod'] . ' ' .  $kurz2['kurz_poradove_cislo'] .
                   ' * '. $kurz2['nazev_kurzu'] .  '</a><br>' ;
            }

            //ve $seznam_jednoho_druhu je seznam kurzu jednoho druhu
            if ($seznam_jednoho_druhu) {
                 $seznam_jednoho_druhu = '<br><h4>' . $druh1['druh_kurzu'] . '</h4>' . '<p>' . $seznam_jednoho_druhu .'</p>';
            }
            else {
                  $seznam_jednoho_druhu = '<br><h4>' . $druh1['druh_kurzu'] . '</h4>' . '<p> ...momentálně nenabizíme</p>';
            }
           //echo "<br><br>------------------seznam druhu--------" . $seznam_jednoho_druhu . "<br>";        // *SEL*


            //priprava obsahu zaklad.stranky druhu - schovat si jmeno podstranky,  druh kurzu, kod druhu kurzu
            $jmenopodstranky = $nalezeny_list_katalogu . '_'  . str_pad($n_druh, 2, "0", STR_PAD_LEFT);
               //echo '<br>jmeno podstranky: ' .  $jmenopodstranky;
            $druhkurzu = $druh1['druh_kurzu'] ;
               //echo '<br>druh kurzu*: ' .  $druhkurzu;
            $druhkurzukod = $druh1['druh_kurzu_kod'];
               //echo '<br>druh kurzu kod*: ' .  $druhkurzukod;


            $poledruhu[$druhkurzukod]=array($jmenopodstranky   ,$druhkurzu);

           //echo "<br>poledruhu";print_r($poledruhu);   // *SEL*

             //*******************zapis podstranky do tabulky stranky
             $sql_query= 'INSERT INTO stranky
                  (list, poradi,
                   nazev_lan1,aktiv_lan1,obsah_lan1,
                   nazev_lan2,aktiv_lan2,
                   nazev_lan3,aktiv_lan3,
                   editor, aut_gen)
                  VALUES ( \''.
                    $jmenopodstranky  .'\', ' .   //jmeno podstranky
                   $n_druh. ','.
                   '\''. $druh1['druh_kurzu'] . '\' , 1 , \'' .  $seznam_jednoho_druhu .  '\', ' .
                   '\''. $druh1['druh_kurzu'] . '\' , 0 , ' .
                   '\''. $druh1['druh_kurzu'] . '\' , 0 , ' .
                   '\'' . $_SESSION ["sess_user"] . '\', \'2_'. str_pad($n_druh, 2, "0", STR_PAD_LEFT) .  '\')' ;
           // echo '<br><br>'; print_r($sql_query); echo '<br>';

            $res3=$this->mdb2->exec($sql_query);

             //****novy sloupec do tabulky opravneni,  pravo na stranku maji role adm a sup
             $sql_query='ALTER TABLE opravneni ADD ' .
                       $nalezeny_list_katalogu . '_'  . str_pad($n_druh, 2, "0", STR_PAD_LEFT) .
                       ' TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT \'0\' ';
            //echo '<br>'; print_r($sql_query);
            $res3=$this->mdb2->exec($sql_query);
            $sql_query = 'UPDATE opravneni SET ' .
                       $nalezeny_list_katalogu . '_'  . str_pad($n_druh, 2, "0", STR_PAD_LEFT) .
                       ' = \'1\' WHERE (role = \'adm\' or  role = \'sup\')';
            //echo '<br>'; print_r($sql_query);
            $res3=$this->mdb2->exec($sql_query);

            $user = $_SESSION ["sess_user"];
            $vysledek = $this->mdb2->query($sql_query);
            $zaznam_opravneni = $vysledek->fetchAll(PDO::FETCH_ASSOC);
            $_SESSION ["sess_prava"] = $zaznam_opravneni;

            $n_druh += 1;

        } //while pro druh
        //***********************vytvorit substranky a opravneni k nim - konec**************
        //**********************************************************************************
        //echo "<br>*poledruhu*";print_r($poledruhu);   // *SEL*



        //*****************************textovy obsah zakladni stranky katalogu z ./obj/anotace_kat_kurzu.inc***************************
        /*
        include './obj/anotace_kat_kurzu.inc';

        $stranotace="";
        foreach ($poledruhu as $druh=>$inf) {

             $stranotace .= $anotace_druhu[$druh];

             $stranotace .=
                    '<p><b>' . $inf['1'] .'</b> <a href="index.php?list=' . $inf['0'] .  '&lang=lan1">' .' ...seznam kurzů zde</a></p>';
             $stranotace .= "<br>";
         }


         $stranotace = '<br><h4>Seznamte se s našimi kurzy...</h4>'
                       . $stranotace;
                       //<p>(seznamy jsou tříděné podle druhu kurzu a seřazené podle kódu kurzu)<br><br><br></p>'


         $sql_query = "UPDATE stranky  SET obsah_lan1 = '" . $stranotace .
                          "' WHERE list ='" . $nalezeny_list_katalogu . "'";
             //echo "<BR>";  echo $sql_query;

         $res1=$this->mdb2->exec($sql_query);
        */



        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">';
        echo '<html>';
        echo '<head>';
        echo '<meta http-equiv="refresh" content="5;url='.$this->action_script.'">';
        echo '<meta http-equiv="Content-Type" CONTENT="text/html; charset=utf-8">';
        echo '<meta http-equiv="Content-Language" content="cs">';
        echo '<title>Změny uloženy</title>';
        echo '</head>';
        echo '<body>';
        echo '<h3>Vybrané kurzy byly publikovány.</h3>';
        echo '<p>... vyčkejte, prosím </p><br><br>';
        echo '</body>';
        echo '</html>';

        }
           else  {echo "<H3>Publikační stránka není jednoznačná.</H3><p>... kontaktujte svého webmastera</p>";}
        }
        else {echo "<H3>Publikační stránka v databázi neexistuje.</H3>";}

    } //function Publikuj


} //class f_katalog_kurzu_seznam





?>
