<?php
/**
 * Object depository for moduly_kurzu.php
 * 
 * @name moduly_kurzu_modul.inc
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
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_input_file.inc";
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_radio.inc";
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_textarea.inc";

/****************************** fce **********************************************************/
    

/****************************************************************************************/



     
class f_moduly_kurzu_modul extends webform {
    private $error_bool=false;
    private $mdb2;
    private $modul_id;
    private $action_script;
    
    private $navrat_script;
    private $edun_prechod_item_id_zpet;
    
    private $modul_kod;
    private $modul_nazev;
    private $kategorie_modulu;
    private $modul_poradove_cislo;
    private $modul_obsah; 
    private $modul_obsah_skripta;
    
    //private $modul_je_v_kurzech;   // pole řetězců (kod a nazev kurzu), neni prvkem formulare(neposila se v POST)
    //private $modul_je_v_castech;   // pole řetězců (kod a nazev casti), neni prvkem formulare(neposila se v POST)
    private $modul_je_v_castech_kurzech;   // pole řetězců (kod a nazev casti,kod a nazev kurzu), neni prvkem formulare(neposila se v POST)
    
    private $vkladany_file;
    
    private $duplikat;
     
    /**
     *Constructor
     *
     *@param string $name Název, pod kterým bude formulář vložen na stránku
     *@param string $action_script Skript, který bude zpracovávat data z formuláře
     *@param $modul_id
     *@param boolean $duplikace true=konstruuji duplikat, bude se jinak ukládat
     *
     * @access  public
     */   
    public function __construct($name,$action_script,$modul_id=false, $duplikace=false) {        
        $this->mdb2 = Middleware\Edun\AppContext::getDb();
        $this->action_script=$action_script;
        if ($duplikace) {
              $this->duplikat=true;
              
        } else {
            $this->duplikat=false;
        }
       
        parent::__construct($name,$this->action_script);

        if($modul_id) {     /*   vyber vseho o modulu , co zobrazim   */           
            $sql_query = 'SELECT
                    id_modul_kurzu,
                    c_kategorie.kod AS kategorie_modulu,
                    modul_kod,
                    modul_poradove_cislo,
                    modul_nazev,
                    modul_obsah,
                    modul_obsah_skripta,
                    modul_kurzu.inserted AS inserted,
                    modul_kurzu.updated AS updated
                FROM modul_kurzu
                left join c_kategorie on c_kategorie.id_c_kategorie=modul_kurzu.id_c_kategorie_FK 
                WHERE id_modul_kurzu = '. $modul_id .';';
            $res=$this->mdb2->query($sql_query);
            
            $modul = $res->fetch(\PDO::FETCH_ASSOC);
            if($modul['updated']) {
                $sql_date=$modul['updated'];
            } else {
                $sql_date=$modul['inserted'];
            }
                                     
            $this->modul_id = new id("modul_id",$modul_id,$sql_date);    /*objekt  id*/
           
             // vybrat kde je v castechkurzech modul pouzity 
            $sql_query = 
                "select   vzb_cast_modul_kurzu.id_modul_kurzu_FK, vzb_cast_modul_kurzu.id_cast_kurzu_FK,
                cast_kurzu.cast_kod, cast_kurzu.deleted as castdel, cast_kurzu.cast_nazev,
                cast_kurzu.cast_poradove_cislo,
                vzb_kurz_cast_kurzu.id_kurz_FK,
                kurz.deleted as kurzdel, 
                kurz.kod_kurzu,
                kurz.kurz_poradove_cislo,
                KATC.kod as kod_kategorie_casti,
                KATK.kod as kod_kategorie_kurzu,
                popis_kurzu.kurz_Nazev 
                from vzb_cast_modul_kurzu
                left join cast_kurzu on  cast_kurzu.id_cast_kurzu = vzb_cast_modul_kurzu.id_cast_kurzu_FK 
                left join c_kategorie as KATC  on KATC.id_c_kategorie = cast_kurzu.id_c_kategorie_FK
                left join vzb_kurz_cast_kurzu on vzb_kurz_cast_kurzu.id_cast_kurzu_FK = vzb_cast_modul_kurzu.id_cast_kurzu_FK
                left join kurz on kurz.id_kurz=vzb_kurz_cast_kurzu.id_kurz_FK
                left join popis_kurzu on popis_kurzu.id_popis_kurzu=kurz.id_popis_kurzu_FK
                left join c_kategorie as KATK  on KATK.id_c_kategorie = kurz.id_c_kategorie_FK
                where vzb_cast_modul_kurzu.id_modul_kurzu_FK=" . $this->modul_id->Get_value() .
                " order by cast_kurzu.cast_kod , kurz.kod_kurzu";
              
            $res=$this->mdb2->query($sql_query);
            $modul_je_v_castech_kurzech = array();
            while($castk = $res->fetch(\PDO::FETCH_ASSOC)) {
                 if ($castk['castdel']) {
                     $castdel= "!!- ";
                 } else {
                     $castdel= "&nbsp;&nbsp;&nbsp;&nbsp;";

                 }
                 if ($castk['kurzdel']) {
                     $kurzdel= "!!- ";

                 } else {
                     $kurzdel= "&nbsp;&nbsp;&nbsp;&nbsp;";

                 }
                 array_push($modul_je_v_castech_kurzech, 
                         $castdel . 
                         $castk['kod_kategorie_casti'] .
                         $castk['cast_poradove_cislo'] .
                         " (kod:" .
                         $castk['cast_kod'] . ") " . $castk['cast_nazev'] . " --> " . 
                         $kurzdel .
                         $castk['kod_kategorie_kurzu'] .
                         $castk['kurz_poradove_cislo'] .
                         " (kod:" .
                         $castk['kod_kurzu'].  ") " . $castk['kurz_Nazev']
                         );
                }

           
                if (isset( $_POST['edun_prechod'])) {       // odkud se do modulu prislo - z casti/ ze seznamu modulu(z modulu po ulozeni)
                    $this->edun_prechod = "prechod_tam";  
                    $this->navrat_script = $_POST['navrat_script'];
                    $this->edun_prechod_item_id_zpet = new id("edun_prechod_item_id_zpet", $_POST['edun_prechod_item_id_zpet'] );  
                } else {
                    $this->edun_prechod = "";  
                    $this->navrat_script = "";
                    $this->edun_prechod_item_id_zpet = new id("edun_prechod_item_id_zpet");
                }

            } else {
                $this->modul_id = new id("modul_id");            /*objekt  id*/
                $modul_je_v_castech_kurzech = array();                
            }        

             /*  vytvorit objekty jednotlivych prvku formulare */
            //$this->modul_kod = new c_webform_input_text("modul_kod","Kód modulu:",10,2,10,@$modul['modul_kod']);
            $this->modul_kod=@$modul['modul_kod'];
            $this->modul_poradove_cislo=@$modul['modul_poradove_cislo'];
            $this->modul_nazev = new c_webform_input_text("modul_nazev","Název modulu:",50,5,200,@$modul['modul_nazev']);
            $this->kategorie_modulu = new c_webform_cselect("kategorie_modulu","Kategorie modulu:",1,"c_kategorie",@$modul['kategorie_modulu']);
            $this->modul_obsah = new c_webform_htmlarea("modul_obsah","Obsah modulu:",90,40,0,64000,@$modul['modul_obsah']); 
            $this->modul_obsah_skripta = new c_webform_htmlarea("modul_obsah_skripta","Obsah skript tohoto modulu:",90,40,0,64000,@$modul['modul_obsah_skripta'],'pro_skripta'); 
            $this->modul_je_v_castech_kurzech= $modul_je_v_castech_kurzech;   //nebude v POST
            $this->vkladany_file = new c_webform_input_file("vkladany_file","Vlož soubor s obrázkem:(.jpg,.png,.gif)",150,0,255);
        } //function construct
    
    
    
    public function Display($part=NULL) {
        
        parent::Display('top');     
        echo ' <input type="hidden" name="navrat_script" id="navrat_script" size="100" value="'.       
             $this->navrat_script .'"  readonly>';
          
        if (!($this->duplikat)) {
            echo 'action*hidden<input type="text" name="action" id="action" value="form">'."\n";
        } else {
            echo 'action*hidden<input type="text" name="action" id="action" value="form">'."\n";
        }
      
        $this->modul_id->Display();    
     
        if (isset($this->edun_prechod_item_id_zpet)) {
            $this->edun_prechod_item_id_zpet->Display();   
        }
           
        if (isset($this->edun_prechod)) {
            echo ' <input type="hidden" name="edun_prechod" id="edun_prechod" size="20" value="'. $this->edun_prechod . '"  readonly>';
        }
     
        echo "<br>";

        //----------------
        if (isset($this->edun_prechod) AND $this->edun_prechod  == "prechod_tam") {    
            echo '<INPUT type="button" id="button_navrat" name="button_navrat" onClick="submit_edit_navrat()" value="Vrať se odkud jsi přišel!">';
        }

        //--------------------   
        if ($this->error_bool) {
            echo "<div ". webform_styles::$error. ">Ve formuláři jsou chyby!</div>";
        }

        if (!($this->duplikat)) {     
            echo '<h3>Modul</h3>';
        } else {
            echo '<h3>Modul - duplikace</h3>'; 
            echo '<p ' .  webform_styles::$pokyn . '>Při duplikaci modulu je nutné změnit tyto údaje -  <b>Název modulu</b></p>';
        }

        if (!($this->duplikat)) {
            if ($this->modul_id->Get_value() ) {    // neni to zadani noveho modulu
                echo "Tento modul je použit v následujících částech --> kurzech:";
                echo '<input type="button" name="button_modul_v_castech_kurzech" id="button_modul_v_castech_kurzech" 
                    value="Skryj" onClick="zobraz_skryj_div_tlacitkem(\'div_modul_v_castech_kurzech\',\'button_modul_v_castech_kurzech\',\'\');">'."\n";

                echo "<div id=div_modul_v_castech_kurzech" .  webform_styles::$formular_text   .  ">" ;   //nebude v POST

                foreach ($this->modul_je_v_castech_kurzech  as  $radekm) {
                    echo  $radekm ."<br>";
                }
                echo '<span '. webform_styles::$drobna_poznamka .'><br>&nbsp;&nbsp;!!- &nbsp;&nbsp; označuje, že položka je znepřístupněna<br></span>';
                echo "</div>" ;
                echo "<BR>\n";
            }   
        }  
        
        echo "<BR>\n";
        //$this->modul_kod->Display();
         echo "Kód modulu:&nbsp;&nbsp;&nbsp;" . $this->modul_kod;
        echo "<BR><BR>\n";
        
         echo "Pořadové (orientační) číslo modulu:&nbsp;&nbsp;&nbsp;" ; 
        $this->kategorie_modulu->Display('JEN_KOD'); 
        echo "<b>"  . $this->modul_poradove_cislo. "</b>";
        echo "<BR><br>\n";
        
        $this->kategorie_modulu->Display();
        echo "<BR><BR>\n"; 
        $this->modul_nazev->Display();
        echo "<BR><BR>\n";
        
             
        
        $this->modul_obsah->Display();
        echo "<BR><BR>\n";
        $this->modul_obsah_skripta->Display();
        
       // $this->vkladany_file->Display();
    
        echo "<BR><BR><BR><BR>\n";
       
        parent::Display('buttons');
        parent::Display('bottom');
        
    } //Display
    
    
    public function Drop() {
        
        if($this->modul_id) {
            
            //kontrola - zda modul lze znepristupnit (vymazat)- tj.ex-li napojeni modulu do nejakeho !nevymazane! casti
            $sql_query = "SELECT * FROM vzb_cast_modul_kurzu
                          left join cast_kurzu on cast_kurzu.id_cast_kurzu=vzb_cast_modul_kurzu.id_cast_kurzu_FK 
                          WHERE id_modul_kurzu_FK= " .  $this->modul_id->Get_value()  .
                          " and deleted=0";
            $res=$this->mdb2->query($sql_query);
           
            //echo "<br> Počet kolikrat je castech - " . $res->rowCount(); // *SEL*
            
            if ($res->rowCount()) {  // modul nelze odstranit(znepristupnit)
                $text_title = "Modul nebude odstraněn" ;
                $text_body = "<h1>Modul nelze odstranit ze seznamu modulů, protože</h1>
                            <h2>modul je použit v částech.  </h2>
                            <p>... vyčkejte, prosím </p><br><br>";
            } else {   // modul znepristupnit     
                $sql_query = 'UPDATE modul_kurzu 
                            SET deleted="1"
                            WHERE id_modul_kurzu="'.$this->modul_id->Get_value().'"';
                $res=$this->mdb2->exec($sql_query);
                $text_title = "Modul odstraněn." ;
                $text_body = "<h1>Modul byl odstraněn ze seznamu modulů</h1><p>... vyčkejte, prosím </p><br><br>";
            }
            
            echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">';
            echo '<html>';
            echo '<head>';
            echo '<meta http-equiv="refresh" content="4;url='.$this->action_script.'">';
            echo '<meta http-equiv="Content-Type" CONTENT="text/html; charset=utf-8">';
            echo '<meta http-equiv="Content-Language" content="cs">';
            echo '<title>. $text_title . </title>';
            echo '</head>';
            echo '<body>';
            echo $text_body;
            echo '</body>';
            echo '</html>';
        }
    }
    
    public function Get_errors() {
        $this->error_bool=false;
        
        //test vnitřních hodnot
        if( $this->modul_nazev->Get_errors() OR $this->kategorie_modulu->Get_errors() OR $this->modul_obsah->Get_errors() ) {
            $this->error_bool=true;
        }    
        
        //   test pro duplikat 
        if ($this->duplikat) { //echo "v Get_errors - modul";
             if (!$this->modul_nazev->byla_zmena_hodnoty() )  {
                 $this->error_bool=true;
                 $this->modul_nazev->set_error("Nezměnil se název modulu!");
             }
        }
        
        
        if($this->error_bool) {
            return true;
        } else {
            return false;
        }
    }
    
    
    
    /**
     * $uloz_dupl=true  - pro duplikaci, ulozi INSERTem
     */
    public function Save($uloz_dupl=false) {        
        if(!$this->error_bool) {
            if(!$this->modul_id->Get_value() or ($uloz_dupl)) {
                try {  //--------------- priprava noveho poradoveho cisla u insertu
                    $sql_query = 
                        'SELECT if(isnull( MAX(modul_poradove_cislo)), 0,MAX(modul_poradove_cislo)) as max from modul_kurzu
                              where modul_kurzu.id_c_kategorie_FK = 
                              (select id_c_kategorie from c_kategorie where kod=\'' . $this->kategorie_modulu->Get_value() .'\')' ;                                               
                
                    $res =$this->mdb2->query($sql_query);
                    $maxi = $res->fetch(\PDO::FETCH_ASSOC);                   
                    $this->modul_poradove_cislo = $maxi['max'] +1;
                    //-----------------------                                                                                                        
                
                    $sql_query = 'INSERT INTO modul_kurzu (
                                           id_c_kategorie_FK, 
                                            
                                            modul_poradove_cislo,    
                                            modul_nazev,
                                            modul_obsah,
                                            modul_obsah_skripta)
                                    VALUES (                            
                            (SELECT id_c_kategorie FROM c_kategorie WHERE kod = '.$this->mdb2->quote($this->kategorie_modulu->Get_value(),"text").'), ' .
                            
                            $this->mdb2->quote($this->modul_poradove_cislo,"integer" ).', '  .
                            $this->mdb2->quote($this->modul_nazev->Get_value()) .', '  .
                            $this->mdb2->quote($this->modul_obsah->Get_value(),"text") .', ' .
                            $this->mdb2->quote($this->modul_obsah_skripta->Get_value()
                                               . $this->vkladany_file->Get_value(),"text")  
                            .')';

                } catch (Exception $e) {
                    print_r($e);
                }
                
                $res=$this->mdb2->exec($sql_query);
                
                $this->modul_id = new id("modul_id",$this->mdb2->lastInsertID());
            } else {
                
                if ($this->kategorie_modulu->byla_zmena_hodnoty()== true) { //------------- priprava noveho poradoveho cisla u update
                    $sql_query = 
                                'SELECT if(isnull( MAX(modul_poradove_cislo)), 0,MAX(modul_poradove_cislo)) as max from modul_kurzu
                                 where modul_kurzu.id_c_kategorie_FK = 
                                 (select id_c_kategorie from c_kategorie where kod=\'' . $this->kategorie_modulu->Get_value() .'\')' ;   
                    $res =$this->mdb2->query($sql_query);
                    $maxi = $res->fetch(\PDO::FETCH_ASSOC);
                    $this->modul_poradove_cislo= $maxi['max']+1; 
            }               
            //--------------------------------------------                 
 
            $sql_query = 'UPDATE modul_kurzu
                                SET                               
                    id_c_kategorie_FK = (SELECT id_c_kategorie FROM c_kategorie WHERE kod = '.$this->mdb2->quote($this->kategorie_modulu->Get_value(),"text").'),
                        
                    modul_poradove_cislo =     '.$this->mdb2->quote( $this->modul_poradove_cislo,"integer" ).', 
                    modul_nazev = '.$this->mdb2->quote($this->modul_nazev->Get_value()).',
                    modul_obsah = '.$this->mdb2->quote($this->modul_obsah->Get_value(),"text").',
                    modul_obsah_skripta = '.$this->mdb2->quote($this->modul_obsah_skripta->Get_value() 
                                                                . $this->vkladany_file->Get_value(),"text")                                                                    
                    .' WHERE id_modul_kurzu ="'.$this->modul_id->Get_value().'"';
                $res=$this->mdb2->exec($sql_query);
            }
            /*****************************************************/
           
            
        if ($this->vkladany_file->Get_value()) {
            //je trebadostat soubor na hosting
        }

          if ( !($_POST['edun_prechod'] == "prechod_tam"  or $_POST['vkladany_file'] )) {            

                echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">';
                echo '<html>';
                echo '<head>';
                echo '<meta http-equiv="Content-Type" CONTENT="text/html; charset=utf-8">';
                echo '<meta http-equiv="Content-Language" content="cs">';
                echo '<title>Změny uloženy</title>';
                echo '</head>';
                echo '<body>';
                echo '<h3>Vaše změny byly v pořádku uloženy</h3>';
                echo '<p>... vyčkejte, prosím </p><br><br>';

                if ($this->vkladany_file->get_value()) {
                    echo '<br><br> JE TREBA DOSTAT SOUBOR NA HOSTING';
                }
                echo '</body>';
                echo '</html>';
            }
        }//error_bool
    } //function Save
    
} //class f_moduly_kurzu_modul





?>
