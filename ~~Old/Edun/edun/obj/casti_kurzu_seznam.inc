<?php
/**
 * Object depository for casti_kurzu.php
 * 
 * @name casti_kurzu_seznam.inc
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
 * Objekt 'webovy formular - seznam vsech casti'
 * vcetne tlacitek Upravit, Přesunout do archivu , Duplikovat... - u kazde casti a tl. Pridat novou cast - pod seznamem
 *
 * Vystup - POST promenne 'action' a 'item_id'
 */
 
class  f_casti_kurzu_seznam extends webform {
    private $error_bool=false;
    private $mdb2;
    
    private $action_script;
    
    public function __construct($name,$action_script) {
        $this->mdb2 = Middleware\Edun\AppContext::getDb();
        $this->action_script=$action_script;
        
        parent::__construct($name,$action_script);
    }
    
    public function Display($por=false) {
       // echo "<BR>"    . $por;
       
           switch ($por) {
       case "kod":
           $poradi="cast_kod";   break;
       case "nazev":
           $poradi="cast_nazev";  break;
       
       case "nazev_kurzu":
           $poradi="kurz_Nazev";  break;
       case "kod_kurzu":
           $poradi="kod_kurzu";  break;       
       case "kurz_por_cislo":
           $poradi="kod_kategorie_kurzu, kurz_poradove_cislo";  break;
       case "cast_kategorie":
           $poradi="cast_kategorie,cast_nazev";  break;
       case "por_cislo":
           $poradi="kod_kategorie_casti, cast_poradove_cislo,kurz_Nazev";  break;
       
       case "pocet_modulu":
            $poradi= "pocet_modulu_v_casti DESC";  break;
       case "pocet_modulu_prazdny_obsah":
            $poradi= "pocet_modulu_v_casti_prazdny_obsah DESC";  break;  
       case "pocet_modulu_prazdny_obsah_skript":
            $poradi= "pocet_modulu_v_casti_prazdny_obsah_skript DESC";  break;  
       case "delka_obsahu_skript":
            $poradi= "delka_obsahu_skript_casti DESC"; break;
       case "delka_obsahu":
            $poradi= "delka_obsahu_casti DESC"; break;
        
        
       default:
           $poradi="cast_kod";
       } 
        
        //echo '<a href="index.php?'.'order=kod'.'&list=katalog_kurzu&app=edun" class=polozkaon>razeni</a>';
           
//        $sql_query = '
//                    SELECT 
//                    cast_kurzu.id_cast_kurzu, cast_kurzu.cast_nazev, cast_kurzu.cast_kod, 
//                    cast_kurzu.cast_poradove_cislo,
//                    c_kategorie.text AS cast_kategorie,
//                    cast_kurzu.inserted, cast_kurzu.updated,
//                    cast_kurzu.deleted, 
//                    kurz.kod_kurzu as kod_kurzu,
//                    kurz.kurz_poradove_cislo,
//                    kurz.deleted as kurz_deleted,
//                    popis_kurzu.kurz_Nazev as kurz_nazev
//                    FROM cast_kurzu 
//                    left join c_kategorie on c_kategorie.id_c_kategorie=cast_kurzu.id_c_kategorie_FK
//                    left join vzb_kurz_cast_kurzu on vzb_kurz_cast_kurzu.id_cast_kurzu_FK = cast_kurzu.id_cast_kurzu 
//                    left join  kurz on vzb_kurz_cast_kurzu.id_kurz_FK= kurz.id_kurz 
//                    left join popis_kurzu on kurz.id_popis_kurzu_FK=popis_kurzu.id_popis_kurzu 
//                    WHERE cast_kurzu.deleted=0 
//                    ORDER BY ' .  $poradi ;   //. ",kod_kurzu";                     
//WHERE deleted=0 ORDER BY ' . $poradi;
            $sql_query = '
            SELECT 
                   (select 
                        count(CK.id_cast_kurzu) 
                        FROM  vzb_cast_modul_kurzu
                        left join  cast_kurzu as CK on vzb_cast_modul_kurzu.id_cast_kurzu_FK = CK.id_cast_kurzu
                        where cast_kurzu.id_cast_kurzu = vzb_cast_modul_kurzu.id_cast_kurzu_FK
                        group by vzb_cast_modul_kurzu.id_cast_kurzu_FK
                    )    AS  pocet_modulu_v_casti ,
                    
                   (select 
                        count(CK.id_cast_kurzu) 
                        FROM  vzb_cast_modul_kurzu
                        left join  cast_kurzu as CK on vzb_cast_modul_kurzu.id_cast_kurzu_FK = CK.id_cast_kurzu
                        left join  modul_kurzu as MK on vzb_cast_modul_kurzu.id_modul_kurzu_FK = MK.id_modul_kurzu
                        where 
                        (cast_kurzu.id_cast_kurzu = vzb_cast_modul_kurzu.id_cast_kurzu_FK)   and  
                        ( char_length(MK.modul_obsah_skripta) is  null)
                        group by vzb_cast_modul_kurzu.id_cast_kurzu_FK
                    )    AS  pocet_modulu_v_casti_prazdny_obsah_skript ,
                    
                    (select 
                        count(CK.id_cast_kurzu) 
                        FROM  vzb_cast_modul_kurzu
                        left join  cast_kurzu as CK on vzb_cast_modul_kurzu.id_cast_kurzu_FK = CK.id_cast_kurzu
                        left join  modul_kurzu as MK on vzb_cast_modul_kurzu.id_modul_kurzu_FK = MK.id_modul_kurzu
                        where 
                        (cast_kurzu.id_cast_kurzu = vzb_cast_modul_kurzu.id_cast_kurzu_FK)   and  
                        ( char_length(MK.modul_obsah) is  null)
                        group by vzb_cast_modul_kurzu.id_cast_kurzu_FK
                    )    AS  pocet_modulu_v_casti_prazdny_obsah ,
                    

                    (select 
                        sum( if( isnull( char_length(modul_obsah_skripta)  ),0,char_length(modul_obsah_skripta) ) ) 
                        FROM  vzb_cast_modul_kurzu		
                        left join  modul_kurzu on vzb_cast_modul_kurzu.id_modul_kurzu_FK = modul_kurzu.id_modul_kurzu
                        where cast_kurzu.id_cast_kurzu = vzb_cast_modul_kurzu.id_cast_kurzu_FK
                        group by vzb_cast_modul_kurzu.id_cast_kurzu_FK
                    )    AS delka_obsahu_skript_casti, 
                    (select 
                        sum( if( isnull( char_length(modul_obsah)  ),0,char_length(modul_obsah) ) ) 
                        FROM  vzb_cast_modul_kurzu		
                        left join  modul_kurzu on vzb_cast_modul_kurzu.id_modul_kurzu_FK = modul_kurzu.id_modul_kurzu
                        where cast_kurzu.id_cast_kurzu = vzb_cast_modul_kurzu.id_cast_kurzu_FK
                        group by vzb_cast_modul_kurzu.id_cast_kurzu_FK
                    )    AS delka_obsahu_casti,


                    cast_kurzu.id_cast_kurzu, cast_kurzu.cast_nazev, cast_kurzu.cast_kod, 
                    cast_kurzu.cast_poradove_cislo,
                    KATC.text AS cast_kategorie,
                    KATC.kod  AS kod_kategorie_casti,

                    KATK.kod  AS kod_kategorie_kurzu,

                    cast_kurzu.inserted, cast_kurzu.updated,
                    cast_kurzu.deleted, 
                    kurz.kod_kurzu as kod_kurzu,
                    kurz.kurz_poradove_cislo,
                    kurz.deleted as kurz_deleted,
                    popis_kurzu.kurz_Nazev as kurz_nazev
                    FROM cast_kurzu 

                    left join c_kategorie  as KATC on  KATC.id_c_kategorie=cast_kurzu.id_c_kategorie_FK
                    left join vzb_kurz_cast_kurzu on vzb_kurz_cast_kurzu.id_cast_kurzu_FK = cast_kurzu.id_cast_kurzu 
                    left join  kurz on vzb_kurz_cast_kurzu.id_kurz_FK= kurz.id_kurz 
                    left join popis_kurzu on kurz.id_popis_kurzu_FK=popis_kurzu.id_popis_kurzu 
                    left join c_kategorie as KATK on  KATK.id_c_kategorie=kurz.id_c_kategorie_FK
                    WHERE cast_kurzu.deleted=0 
                    ORDER BY ' .  $poradi ;   //. ",kod_kurzu";  

//
        //print_r($sql_query);                
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
       
        echo '<h3>Části kurzu</h3>';
        echo '<INPUT type="button" name="new_cast" onClick="submit_new()" value="Přidat novou část"><br><br>';
            echo '<table '. webform_styles::$casti_table . '>'."\n";   //class="tabpismo"
            //rozvrzeni sloupcu a hlavicka tabulky
            echo '<tr>'."\n";
            echo '<th '.webform_styles::$casti_table_spacing['v_kurzu'].'>Část je zařazena v kurzu&nbsp;</th>' ;
            echo '</tr><tr>';
            
            //echo '<th '.webform_styles::$casti_table_spacing['before'].'>&nbsp</th>'."\n";
      echo '<th colspan=2'.webform_styles::$casti_table_spacing['v_kurzu_poradove_cislo'].'>' .
                '<a href="index.php?'.'order=kurz_por_cislo'.'&list=casti_kurzu&app=edun" '. webform_styles::$podtrzeni  .  ' >Pořadové číslo</a>'
                . '&nbsp</th>'."\n";
      
            echo '<th '.webform_styles::$casti_table_spacing['v_kurzu_kod'].'>' .
                '<a href="index.php?'.'order=kod_kurzu'.'&list=casti_kurzu&app=edun" '. webform_styles::$podtrzeni  .  ' >Kód&nbspkurzu</a>'
                . '&nbsp</th>'."\n";
            echo '<th '.webform_styles::$casti_table_spacing['v_kurzu_nazev'].'>' . 
                '<a href="index.php?'.'order=nazev_kurzu'.'&list=casti_kurzu&app=edun" '.  webform_styles::$podtrzeni . ' >Název kurzu</a>'    
                .'</th>'."\n";
            
      echo '<th colspan=2'.webform_styles::$casti_table_spacing['cast_poradove_cislo'].
                 '><a href="index.php?'.'order=por_cislo'.'&list=casti_kurzu&app=edun" '. webform_styles::$podtrzeni . '>Pořadové číslo</a></th>'.
                 "\n";
      
            echo '<th '.webform_styles::$casti_table_spacing['cast_kod'].
                 '><a href="index.php?'.'order=kod'.'&list=casti_kurzu&app=edun" '. webform_styles::$podtrzeni . '>Kód části</a></th>'.
                 "\n";
            echo '<th '.webform_styles::$casti_table_spacing['cast_nazev'].
                 '><a href="index.php?'.'order=nazev'.'&list=casti_kurzu&app=edun" '.  webform_styles::$podtrzeni .'>Název části</a></th>'."\n";
            echo '<th '.webform_styles::$casti_table_spacing['cast_kategorie'].
                 '><a href="index.php?'.'order=cast_kategorie'.'&list=casti_kurzu&app=edun"'.  webform_styles::$podtrzeni . '>Kategorie casti</a></th>'."\n";
  
  echo '<th '.webform_styles::$casti_table_spacing['pocet_modulu'].'>&nbsp&nbspPočet&nbspmodulů<br>
        <a href="index.php?'.'order=pocet_modulu'.'&list=casti_kurzu&app=edun" '.  webform_styles::$podtrzeni .'>
        počet&nbsp</a>/ <br>
        <a href="index.php?'.'order=pocet_modulu_prazdny_obsah'.'&list=casti_kurzu&app=edun" '.  webform_styles::$podtrzeni .'>
        prázdný obsah&nbsp</a>/ <br>
        <a href="index.php?'.'order=pocet_modulu_prazdny_obsah_skript'.'&list=casti_kurzu&app=edun" '.  webform_styles::$podtrzeni .'>
        prázdná skripta&nbsp</a>
        </th>'."\n";
                 
  echo '<th '.webform_styles::$casti_table_spacing['pocet_modulu'].'>        
        <a href="index.php?'.'order=delka_obsahu'.'&list=casti_kurzu&app=edun" '.  webform_styles::$podtrzeni .'>
        délka obsahu&nbsp</a>/ <br>
        <a href="index.php?'.'order=delka_obsahu_skript'.'&list=casti_kurzu&app=edun" '.  webform_styles::$podtrzeni .'>
        délka obsahu&nbspskript</a>
        </th>'."\n";
          
            echo '<th '.webform_styles::$casti_table_spacing['buttons'].'>&nbsp</th>'."\n";
            echo '<th '.webform_styles::$casti_table_spacing['over'].'>&nbsp</th>'."\n";
            echo '</tr>'."\n";
            
            //data=casti
/* W */     while ($cast=$res->fetch(\PDO::FETCH_ASSOC)) {
                if($cast['updated']) {
                    $sql_date = $cast['updated'];
                }
                else {
                    $sql_date = $cast['inserted'];
                }
                echo '<tr>'."\n";
                
//                      //-----------------------------delka_obsahu_skript_casti-----------------
//                      $sql_query1 = 'select 
//                        sum( if( isnull( char_length(modul_obsah_skripta)  ),0,char_length(modul_obsah_skripta) ) ) as delka_obsahu_skriptz
//                        FROM  vzb_cast_modul_kurzu		
//                        left join  modul_kurzu on vzb_cast_modul_kurzu.id_modul_kurzu_FK = modul_kurzu.id_modul_kurzu
//                        where ' . $cast['id_cast_kurzu']  . '= vzb_cast_modul_kurzu.id_cast_kurzu_FK
//                        group by vzb_cast_modul_kurzu.id_cast_kurzu_FK';
//                      
//                      $res1 = $this->mdb2->query($sql_query1);
//                      $cast_doplnujici_udaje = $res1->fetch(\PDO::FETCH_ASSOC);
//                      //print_r($cast_doplnujici_udaje);
//                      //------------------------------------------------                         
               
         //echo '<td>&nbsp</td>'."\n";
         echo '<td '.webform_styles::$casti_table_td_doprava.'>'.$cast['kod_kategorie_kurzu']. '&nbsp</td>'."\n";
         echo '<td '.webform_styles::$casti_table_td_doprava.'>'.$cast['kurz_poradove_cislo']. '&nbsp</td>'."\n";
                echo '<td '.webform_styles::$casti_table_td_doprava.'>'.$cast['kod_kurzu']. '&nbsp&nbsp&nbsp</td>'."\n";
                
                echo '<td>';
                        if ($cast['kurz_deleted'] ) {
                              echo '!!-';
                        }
                echo $cast['kurz_nazev'] ;
                echo  '&nbsp&nbsp</td>'."\n";
         
        // echo '<td>&nbsp</td>'."\n";
         echo '<td '.webform_styles::$casti_table_td_doprava.'>'.$cast['kod_kategorie_casti']. '&nbsp</td>'."\n";
         echo '<td '.webform_styles::$casti_table_td_doprava.'>'.$cast['cast_poradove_cislo']. '&nbsp</td>'."\n";                
                echo '<td ' . webform_styles::$casti_table_td_doprava. '>'.$cast['cast_kod'].'&nbsp&nbsp</td>'."\n";
                echo '<td>'.$cast['cast_nazev'].'&nbsp&nbsp</td>'."\n";
                
                echo '<td>'.$cast['cast_kategorie'].'&nbsp&nbsp</td>'."\n";
                
                echo '<td'. webform_styles::$casti_table_spacing['pocet_modulu'] .'>'  .
                        $cast['pocet_modulu_v_casti']. ' / ' .
                        $cast['pocet_modulu_v_casti_prazdny_obsah'] . ' / ' .
                        $cast['pocet_modulu_v_casti_prazdny_obsah_skript'] .
                     '&nbsp&nbsp</td>'."\n";
                echo '<td'. webform_styles::$casti_table_spacing['pocet_modulu'] .'>'. 
                            $cast['delka_obsahu_casti'] . ' / ' . 
                            $cast['delka_obsahu_skript_casti'].                       
                     
                     '&nbsp&nbsp</td>'."\n";
              


                echo '<td><a href="javascript: submit_edit(\''.id_code::code($cast['id_cast_kurzu'],$sql_date).'\') "><img src="img/b_edit.png" alt="Upravit část" title="Upravit část" border="0"></a>
                          <a href="javascript: submit_drop(\''.id_code::code($cast['id_cast_kurzu'],$sql_date).'\') "><img src="img/b_drop.png" alt="Část přesunout z katalogu do archivu" title="Část přesunout z katalogu do archivu" border="0"></a>
                          <a href="javascript: submit_dupl(\''.id_code::code($cast['id_cast_kurzu'],$sql_date).'\') "><img src="img/b_copy.png" alt="Duplikovat část" title="Duplikovat část" border="0"></a>
                      </td>'."\n";  
                echo '<td>&nbsp</td>'."\n";
                echo '</tr>'."\n";
                
                //print_r($modul);
            }
            echo '</table>';
        echo '<br><INPUT type="button" name="new_cast" onClick="submit_new()" value="Přidat novou část">';
        /*   toto bylo yakomentovane
        if ($_SESSION ['sess_prava']['role'] == "adm")  {
             echo '<INPUT type="button" name="poradove_cislo_casti" onClick="submit_poradove_cislo_casti()" value="Nastavit pořadové číslo částí">';     
        }
        */ 
        parent::Display('bottom');
    
    }   //function Display  
     
} //class f_casti_kurzu_seznam


?>
