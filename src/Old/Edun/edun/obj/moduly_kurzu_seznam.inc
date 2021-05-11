<?php
/**
 * Object depository for moduly_kurzu.php
 * 
 * @name moduly_kurzu_seznam.inc
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
 * Objekt 'webovy formular - seznam vsech modulu'
 * vcetne tlacitek Upravit, Přesunout do archivu , Duplikovat - u kazdeho modulu a tl. Pridat novy modul - pod seznamem
 *
 * Vystup - POST promenne 'action' a 'item_id'
 */
 
class  f_moduly_kurzu_seznam extends webform {
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
           $poradi="modul_kod";   break;
       case "nazev":
           $poradi="modul_nazev";  break;
       case "modul_kategorie":
           $poradi="modul_kategorie, modul_nazev";  break;
       case "por_cislo":
           $poradi="kod_kategorie_modulu,modul_poradove_cislo";  break;
       
       case "delka_obsahu":
           $poradi="delka_obsahu_modulu DESC";  break;
       case "delka_obsahu_skript":
           $poradi="delka_obsahu_skript_modulu  DESC";  break;
       
       default:
           $poradi="modul_kod";
       }      
         
       function styl_zahlavi(){
           
       }
        $sql_query = 'SELECT id_modul_kurzu, modul_nazev, modul_kod, modul_obsah,
                     if( isnull( char_length(modul_obsah_skripta)  ),0,char_length(modul_obsah_skripta) ) as  delka_obsahu_skript_modulu,
                     if( isnull( char_length(modul_obsah)  ),0,char_length(modul_obsah) ) as  delka_obsahu_modulu,
                      modul_poradove_cislo,
                      c_kategorie.text AS modul_kategorie,
                      c_kategorie.kod as kod_kategorie_modulu, 
                      modul_kurzu.inserted as inserted, modul_kurzu.updated as updated
                      FROM modul_kurzu 
                      left join c_kategorie on c_kategorie.id_c_kategorie=modul_kurzu.id_c_kategorie_FK
                      WHERE  modul_kurzu.deleted=0 ORDER BY ' . $poradi;
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
       
        echo '<h3>Moduly kurzu</h3>';
        echo '<INPUT type="button" name="new_modul" onClick="submit_new()" value="Přidat nový modul">';
            echo '<table '. webform_styles::$moduly_table . '>'."\n";   //class="tabpismo"
            //rozvrzeni sloupcu a hlavicka tabulky
            echo '<tr>'."\n";
            echo '<th '.webform_styles::$moduly_table_spacing['before'].'>&nbsp</th>'."\n";
                 
   echo '<th colspan=2'.webform_styles::$moduly_table_spacing['modul_poradove_cislo'].
                 '><a href="index.php?'.'order=por_cislo'.'&list=moduly_kurzu&app=edun"' . webform_styles::$podtrzeni .  '>Pořadové číslo</a></th>'.
                 "\n";
            
            
            echo '<th '.webform_styles::$moduly_table_spacing['modul_kod'].
                 '><a href="index.php?'.'order=kod'.'&list=moduly_kurzu&app=edun"' . webform_styles::$podtrzeni . '>Kód modulu</a></th>'.
                 "\n";
            echo '<th '.webform_styles::$moduly_table_spacing['modul_nazev'].
                 '><a href="index.php?'.'order=nazev'.'&list=moduly_kurzu&app=edun"'.  webform_styles::$podtrzeni . '>Název modulu</a></th>'."\n";
            echo '<th '.webform_styles::$moduly_table_spacing['modul_kategorie'].
                 '><a href="index.php?'.'order=modul_kategorie'.'&list=moduly_kurzu&app=edun"'.  webform_styles::$podtrzeni . '>Kategorie modulu</a></th>'."\n";
      
       echo '<th '.webform_styles::$moduly_table_spacing['delka_obsahu_modulu'].                     
            '><a href="index.php?'.'order=delka_obsahu'.'&list=moduly_kurzu&app=edun"'.  webform_styles::$podtrzeni . '>délka obsahu</a></th>'."\n";     
       echo '<th '.webform_styles::$moduly_table_spacing['delka_obsahu_skript_modulu'].                     
            '><a href="index.php?'.'order=delka_obsahu_skript'.'&list=moduly_kurzu&app=edun"'.  webform_styles::$podtrzeni . '>délka obsahu skript</a></th>'."\n";
           
            
            echo '<th '.webform_styles::$moduly_table_spacing['buttons'].'>&nbsp</th>'."\n";
            echo '<th '.webform_styles::$moduly_table_spacing['over'].'>&nbsp</th>'."\n";
            echo '</tr>'."\n";
            
            //data=moduly
            while ($modul=$res->fetch(\PDO::FETCH_ASSOC)) {
                if($modul['updated']) {
                    $sql_date = $modul['updated'];
                }
                else {
                    $sql_date = $modul['inserted'];
                }
                echo '<tr>'."\n";
                echo '<td>&nbsp</td>'."\n";
                
                echo '<td ' . webform_styles::$moduly_table_td_doprava. '>'.$modul['kod_kategorie_modulu'].'&nbsp&nbsp</td>'."\n";
                echo '<td ' . webform_styles::$moduly_table_td_doprava. '>'.$modul['modul_poradove_cislo'].'&nbsp&nbsp</td>'."\n";
                
                echo '<td ' . webform_styles::$moduly_table_td_doprava. '>'.$modul['modul_kod'].'&nbsp&nbsp</td>'."\n";
                                
                echo '<td>'.$modul['modul_nazev'].'&nbsp&nbsp</td>'."\n";
                echo '<td>'.$modul['modul_kategorie'].'&nbsp&nbsp</td>'."\n";
       
       echo '<td' . webform_styles::$moduly_table_spacing['delka_obsahu_modulu']. '>'.$modul['delka_obsahu_modulu'].'&nbsp&nbsp</td>'."\n";         
       echo '<td' . webform_styles::$moduly_table_spacing['delka_obsahu_skript_modulu']. '>'.$modul['delka_obsahu_skript_modulu'].'&nbsp&nbsp</td>'."\n";

                echo '<td><a href="javascript: submit_edit(\''.id_code::code($modul['id_modul_kurzu'],$sql_date).'\') "><img src="img/b_edit.png" alt="Upravit modul" title="Upravit modul" border="0"></a>
                          <a href="javascript: submit_drop(\''.id_code::code($modul['id_modul_kurzu'],$sql_date).'\') "><img src="img/b_drop.png" alt="Modul přesunout z katalogu do archivu" title="Modul přesunout z katalogu do archivu" border="0"></a>
                          <a href="javascript: submit_dupl(\''.id_code::code($modul['id_modul_kurzu'],$sql_date).'\') "><img src="img/b_copy.png" alt="Duplikovat modul" title="Duplikovat modul" border="0"></a>
                      </td>'."\n";  
                echo '<td>&nbsp</td>'."\n";
                echo '</tr>'."\n";
                
                //print_r($modul);
            }
            echo '</table>';
        echo '<br><INPUT type="button" name="new_modul" onClick="submit_new()" value="Přidat nový modul">';
        /*
        if ($_SESSION ['sess_prava']['role'] == "adm")  {
             echo '<INPUT type="button" name="poradove_cislo_modulu" onClick="submit_poradove_cislo_modulu()" value="Nastavit pořadové číslo modulů ">';     
        }
        */
        parent::Display('bottom');
  
        
  }  //function Display
    
    
    
     
} //class f_moduly_kurzu_seznam



?>
