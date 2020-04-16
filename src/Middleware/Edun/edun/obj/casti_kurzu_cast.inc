<?php
/**
 * Object depository for casti_kurzu.php
 * 
 * @name casti_kurzu_cast.inc
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



     
class f_casti_kurzu_cast extends webform {
    private $error_bool=false;
    private $mdb2;
    private $cast_id;
    private $action_script;

    
   // private $item_id_promodul;  
    private $edun_prechod_item_id_tam; //pro prechod na edititko modulu  
    private $edun_prechod_item_id_zpet; 
    
    private $cast_kod;
    private $cast_nazev;
    private $cast_poradove_cislo;
    private $kategorie_casti;
    private $cast_nazev_skripta; 
    
    private $moduly_casti;
    private $moduly_casti_obsahy;  // retezec html, neni prvkem formulare(neposila se v POST) 
   
    
    private $cast_je_v_kurzech;   // pole řetězců (kod a nazev kurzu), neni prvkem formulare(neposila se v POST)
    
    private $duplikat;
     
    /**
     *Constructor
     *
     *@param string $name Název, pod kterým bude formulář vložen na stránku
     *@param string $action_script Skript, který bude zpracovávat data z formuláře
     *@param $cast_id
     *@param boolean $duplikace true=konstruuji duplikat, bude se jinak ukládat
     *
     * @access  public
     */   
    public function __construct($name,$action_script,$cast_id=false, $duplikace=false) {
        $this->mdb2 = Middleware\Edun\AppContext::getDb();
        $this->action_script=$action_script;
        if ($duplikace) {
              $this->duplikat=true;
              
        } else {
            $this->duplikat=false;
        }
       
        parent::__construct($name,$this->action_script);
        
        if($cast_id) {
                        /*   vyber vseho o casti , co zobrazim   */
            $sql_query = 'SELECT
                    id_cast_kurzu,
                    c_kategorie.kod AS kategorie_casti,
                    cast_kod,
                    cast_poradove_cislo,
                    cast_nazev,
                    cast_nazev_skripta,
                    cast_kurzu.inserted AS inserted,
                    cast_kurzu.updated AS updated
                FROM cast_kurzu
                left join c_kategorie on c_kategorie.id_c_kategorie=cast_kurzu.id_c_kategorie_FK 
                WHERE id_cast_kurzu = '. $cast_id .';';
            $res=$this->mdb2->query($sql_query);
            
            $cast = $res->fetch(\PDO::FETCH_ASSOC);
            if($cast['updated']) {
                $sql_date=$cast['updated'];
            }
            else {
                $sql_date=$cast['inserted'];
            }
            $sql_date_casti =  $sql_date;
            
            //echo "<br>sql_date*" . $sql_date_casti;   /*SEL*/ 
            $this->cast_id = new id("cast_id",$cast_id,$sql_date_casti);    /*objekt  id*/
            
            //**********************************************
            
            
            // vybrat kde je  cast pouzita
            $sql_query = "
                select 
                    vzb_kurz_cast_kurzu.id_kurz_FK, 
                    kurz.kod_kurzu, kurz.kurz_poradove_cislo,
                    c_kategorie.kod as kod_kategorie_kurzu,
                    kurz.deleted, popis_kurzu.kurz_Nazev
                from vzb_kurz_cast_kurzu
                    join kurz on  kurz.id_kurz = vzb_kurz_cast_kurzu.id_kurz_FK
                    join popis_kurzu on popis_kurzu.id_popis_kurzu = kurz.id_popis_kurzu_FK
                    join c_kategorie on c_kategorie.id_c_kategorie= kurz.id_c_kategorie_FK
                where vzb_kurz_cast_kurzu.id_cast_kurzu_FK="   . $this->cast_id->Get_value() . " order by kurz.kurz_poradove_cislo";
            $res=$this->mdb2->query($sql_query);
            $cast_je_v_kurzech = array();
            while($kurz = $res->fetch(\PDO::FETCH_ASSOC)) {
                if ($kurz['deleted']) {
                    $kurzdel= "!!- ";
                } else {
                    $kurzdel= "&nbsp;&nbsp;&nbsp;&nbsp;";
                }
                 
                array_push($cast_je_v_kurzech, $kurzdel .
                        $kurz['kod_kategorie_kurzu'] .
                        $kurz['kurz_poradove_cislo'] .
                           ' (kod:' .      $kurz['kod_kurzu'] . ") " . $kurz['kurz_Nazev']);
                }
            
/**************************************moduly casti***/
            
            $sql_query = 'SELECT
                            vzb_cast_modul_kurzu.id_modul_kurzu_FK,
                            vzb_cast_modul_kurzu.id_cast_kurzu_FK,
                            modul_kurzu.deleted 
                            FROM vzb_cast_modul_kurzu
                            left join modul_kurzu on  vzb_cast_modul_kurzu.id_modul_kurzu_FK = modul_kurzu.id_modul_kurzu 
                            WHERE id_cast_kurzu_FK = '.$this->cast_id->Get_value()  . 
                            ' and (modul_kurzu.deleted=0) '  .
                            ' order by razeni'. ';';
              // echo ("<br>" . $sql_query . "<br>");   /*SEL*/           
            $res=$this->mdb2->query($sql_query);
            $moduly_casti = array();
            $moduly_casti_obsahy='';
            while($moduly_casti_polozka = $res->fetch(\PDO::FETCH_ASSOC)) {
                array_push($moduly_casti,$moduly_casti_polozka['id_modul_kurzu_FK']);

                // vybrat z modulu kurzu obsahy - naskladat do moduly_casti_obsahy                   
                   $sql_query_1 = 'SELECT id_modul_kurzu,
                            modul_kod, modul_nazev, modul_obsah, modul_poradove_cislo, 
                            modul_kurzu.updated as upda, modul_kurzu.inserted as inse,
                            c_kategorie.kod
                            FROM modul_kurzu
                            left join c_kategorie on c_kategorie.id_c_kategorie=modul_kurzu.id_c_kategorie_FK
                            WHERE id_modul_kurzu = '. $moduly_casti_polozka['id_modul_kurzu_FK'] .
                            ' and (modul_kurzu.deleted=0)' ;
                      //echo ("<br>" . $sql_query_1. "<br>");   // *SEL*
                   $res_1 =$this->mdb2->query($sql_query_1);
                   while($modul_casti_obsah = $res_1->fetch(\PDO::FETCH_ASSOC)) {
                            if($modul_casti_obsah['upda']) {
                                $sql_date = $modul_casti_obsah['upda'];    }
                            else {
                                $sql_date = $modul_casti_obsah['inse'];   }
                                //print_r ("<br>sql pro modul" . $sql_date ." <br> " );    
                       
                           $pripona =  "_" .  str_pad($moduly_casti_polozka['id_cast_kurzu_FK'],4, "0", STR_PAD_LEFT) .
                             "_" .  str_pad($moduly_casti_polozka['id_modul_kurzu_FK'],4, "0", STR_PAD_LEFT) ;
                       
                       
                           $moduly_casti_obsahy .=
                             "<DIV id='div_modul_nazev_jeden" . $pripona . 
                             "' " . webform_styles::$formular_cast_modul_nazev  . " >
                             --modul-- " .  
                                   $modul_casti_obsah['kod'] . 
                                   $modul_casti_obsah['modul_poradove_cislo'] .
                               " (kod:" .$modul_casti_obsah['modul_kod'] . ")  " .  $modul_casti_obsah['modul_nazev'] . 
                             "&nbsp; &nbsp;" .
                           
                                   
                             "<input type='button' name='button_obsah_modulu". $pripona . "' " . 
                             " id='button_obsah_modulu". $pripona . "' " . 
                             " value='Zobraz obsah' " .
                             " onClick=\"zobraz_skryj_div_tlacitkem(" .  
                                      "'div_modul_obsah_jeden" . $pripona . "'," .
                                      "'button_obsah_modulu". $pripona . "'," . "'obsah'" .       
                                      ");\" " .
                             webform_styles::$drobny_butt   .  "> " . 
                                   
           //"*** " . $modul_casti_obsah['id_modul_kurzu'] . " " .id_code::code($modul_casti_obsah['id_modul_kurzu'],$sql_date).                                             
              "&nbsp;&nbsp;&nbsp;
               <a href=\"javascript: submit_edit_prechod('". id_code::code($modul_casti_obsah['id_modul_kurzu'],$sql_date).   
                                                   "','"    . id_code::code($cast_id, $sql_date_casti) .       "')" .                                                    
              "\">   <img src='img/b_edit.png' alt='p' title='Přejdi na modul' border='0'></a>" .                                               
                                   
                             "</DIV>" .
                                   
                             "<DIV id='div_modul_obsah_jeden".  $pripona . 
                             "' " . webform_styles::$formular_cast_modul_obsah  .  
                             " >" . $modul_casti_obsah['modul_obsah'] . "</DIV>" ;
                                              
                   }  //while  modul_casti_obsah
            }  //while  moduly_casti_polozka   
           
 
            
            //echo "<br> v constructu kurz - Smoduly_casti a Smoduly_casti_obsahy <br>";
            //print_r($moduly_casti);            //  *SEL*   ladici
            //print_r($moduly_casti_obsahy);     //  *SEL*   ladici
            /*****************************************/
    
$this->edun_prechod_item_id_tam = new id("edun_prechod_item_id_tam");   //priprava pro  prechod na edititko modulu
$this->edun_prechod_item_id_zpet = new id("edun_prechod_item_id_zpet"); 

$this->edun_prechod = "";  
            
            
        } else {   //nova cast
            $this->cast_id = new id("cast_id");            /*objekt  id*/
            $cast_je_v_kurzech = array();
            $moduly_casti = array();
            $moduly_casti_obsahy='';
        }
   
     // echo "<br>this-cast_id*"; print_r($this->cast_id);  /*SEL*/
        

  
       
         /*  vytvorit objekty jednotlivych prvku formulare */
        //$this->cast_kod = new c_webform_input_text("cast_kod","Kód části:",10,2,10,@$cast['cast_kod']);
        $this->cast_kod=@$cast['cast_kod'];
        $this->cast_poradove_cislo=@$cast['cast_poradove_cislo'];
          
        $this->cast_nazev = new c_webform_input_text("cast_nazev","Název části:",50,5,200,@$cast['cast_nazev']);
        
        $this->kategorie_casti = new c_webform_cselect("kategorie_casti","Kategorie části:",1,"c_kategorie",@$cast['kategorie_casti']);
        
        //$this->cast_nazev_skripta = new c_webform_input_text("cast_nazev_skripta","Název kapitoly(části)ve skriptech:",50,0,200,@$cast['cast_nazev_skripta']);
        $this->cast_nazev_skripta = new c_webform_htmlarea("cast_nazev_skripta","Název kapitoly(části) ve skriptech:",90,2,0,500,@$cast['cast_nazev_skripta']);
       
        //$this->cast_obsah = new c_webform_htmlarea("modul_obsah","Obsah modulu:",90,40,0,4000,@$modul['modul_obsah']);
        //$this->modul_obsah_skripta = new c_webform_htmlarea("modul_obsah_skripta","Obsah skript tohoto modulu:",90,40,0,5000,@$modul['modul_obsah_skripta']);
        
        $this->cast_je_v_kurzech= $cast_je_v_kurzech;   //nebude v POST - pouze se zobrazuje
        
        
        $this->moduly_casti = new c_webform_cselect_multi("moduly_casti",
                                     "Moduly části a jejich obsah:<BR>"  ,
                                     1,"view_c_moduly",@$moduly_casti);
        
        $this->moduly_casti_obsahy= $moduly_casti_obsahy;      //nebude v POST
         /*****/    
              // echo ( "<br><br>** this-moduly casti v constructu cast-old value* " .  implode(',',$this->moduly_casti->Get_old_value()) . "<br>") ;  // *SEL* 
              //print_r ($this->moduly_casti->Get_old_value());
        
        
    } //function construct
    
    
    
    public function Display($part=NULL) {
        parent::Display('top');
   
     //echo '*pole navrat_script*hidden:';
     echo '<input type="hidden" name="navrat_script" id="navrat_script" size="100" value="'.       
             $this->action_script .
          '"  readonly>';
     //echo "<br>"; 
        
     if (!($this->duplikat))   {
           //echo '*formularove pole action*hidden:';
           echo '<input type="hidden" name="action" id="action" value="form"  readonly>'."\n";
           //echo "<br>"; //*SEL*
           // echo '<input type="hidden" name="action" id="action" value="form">'."\n";
     }
     else {
        //echo '*formularove pole action*hidden:';
        echo '<input type="hidden" name="action" id="action" value="form_dupl"  readonly>'."\n";
        //echo "<br>"; //*SEL*
        // echo '<input type="hidden" name="action" id="action" value="form_dupl">'."\n";
     }        
     
     $this->cast_id->Display();
        //echo "<BR><BR>\n";
     
if (isset($this->edun_prechod_item_id_tam)) {     
    $this->edun_prechod_item_id_tam->Display();    //pro prechod na edititko modulu
}
if (isset($this->edun_prechod_item_id_zpet)) {
    $this->edun_prechod_item_id_zpet->Display();  
}
if (isset($this->edun_prechod)) {
    //$this->edun_prechod->Display();    //pro prechod na edititko modulu 
    //echo '*pole edun_prechod*hidden:';
    echo '<input type="hidden" name="edun_prechod" id="edun_prechod" size="20" value="'.       
             $this->edun_prechod .
          '"  readonly>';
    //echo "<br>"; 
}
 
     
     if ($this->error_bool) {
           echo "<div ". webform_styles::$error. ">Ve formuláři jsou chyby!</div>";
     }
        
        
     if (!($this->duplikat)) {     
        echo '<h3>Část</h3>';
     }
     else {
          echo '<h3>Část - duplikace</h3>'; 
            echo '<p ' .  webform_styles::$pokyn . '>Při duplikaci Části je nutné změnit tyto údaje -   <b>Název části</b>.
                  </p>';
      }
        
        
     if (!($this->duplikat)) {
        if ($this->cast_id->Get_value() )   // neni to zadani noveho casti
        {
        echo "Tato část je použita v následujících katalogových kurzech:";
        
        echo '<input type="button" name="button_cast_v_kurzech" id="button_cast_v_kurzech" 
            value="Skryj" onClick="zobraz_skryj_div_tlacitkem(\'div_cast_v_kurzech\',\'button_cast_v_kurzech\',\'\');">'."\n";
        
        echo "<div id=div_cast_v_kurzech" .  webform_styles::$formular_text   .  ">" ;   //nebude v POST
        
            foreach ($this->cast_je_v_kurzech  as  $radekm) {
                echo  $radekm ."<br>";
            }
        
        echo '<span '. webform_styles::$drobna_poznamka .'><br>&nbsp;&nbsp;!!- &nbsp;&nbsp; označuje, že položka je znepřístupněna<br></span>';

        echo "</div>" ;
        echo "<BR>\n";
        }   
     }  
        
        echo "<BR>\n";
       
        //$this->cast_kod->Display();
        echo "Kód části:&nbsp;&nbsp;&nbsp;" . $this->cast_kod;
        echo "<BR><BR>\n";
        
        echo "Pořadové (orientační) číslo části:&nbsp;&nbsp;&nbsp;" ; 
        $this->kategorie_casti->Display('JEN_KOD'); 
        echo "<b>" . $this->cast_poradove_cislo . "</b>";
        echo "<BR><br>\n";
        
        $this->kategorie_casti->Display();
        echo "<BR><BR>\n"; 
        
        $this->cast_nazev->Display();
        echo "<BR><BR>\n";
        //$this->cast_nazev_skripta->Display();   zruseno
        //echo "<BR><BR>\n";        
             
 
        echo "<div id=div_obsah_casti>";
          //if (($this->zobrazeni_obsahu->get_value()==2) or ($this->zobrazeni_obsahu->get_value()==3))
          ////    {echo " style='display:block'";} else {echo " style='display:none'";}
          // {echo webform_styles::$display_block; } else {echo webform_styles::$display_none; }
     
          $this->moduly_casti->Display();
          //echo "<BR><BR>\n";
          echo "<div " .  webform_styles::$formular_text   .  ">" . $this->moduly_casti_obsahy . "</div>" ;
          echo "<BR>\n";
        echo "</div>";
        
        echo "<BR><BR><BR><BR>\n";
       
        parent::Display('buttons');
        parent::Display('bottom');
        
    } //Display
    
    
    public function Drop() {
        //echo "<BR>* " ; print_r($this->modul_id);  //*SEL*
        //echo "<BR>* " ; print_r($this->modul_id->Get_value()); 
        //echo "<BR>* " ;  print_r($this->modul_kod) ;    //*SEL*
        
        if($this->cast_id) {
            
            //kontrola - zda  cast lze znepristupnit (vymazat) - tj.ex-li napojeni casti do nejakeho !nevymazaneho! kurzu
            $sql_query = "SELECT * FROM vzb_kurz_cast_kurzu 
                          left join kurz on kurz.id_kurz= vzb_kurz_cast_kurzu.id_kurz_FK 
                         WHERE id_cast_kurzu_FK= " .  $this->cast_id->Get_value() .
                         " and deleted=0" ;
            //echo  "<br>"  . $sql_query ;
            
            $res=$this->mdb2->query($sql_query);
           
            //echo "<br> Počet kolikrat je v kurzech - " . $res->rowCount(); // *SEL*
            
            if ($res->rowCount()) {  // cast nelze odstranit(znepristupnit)
              $text_title = "Část nebude odstraněna" ;
              $text_body = "<h1>Část nelze odstranit ze seznamu částí, </h1>
                            <h2>protože je použita v kurzech.  </h2>
                            <p>... vyčkejte, prosím </p><br><br>";
            }       
            else      // cast znepristupnit       
            {
              $sql_query = 'UPDATE cast_kurzu 
                            SET deleted="1"
                            WHERE id_cast_kurzu="'.$this->cast_id->Get_value().'"';
              $res=$this->mdb2->exec($sql_query);
              //echo "<br>" . $sql_query ;   // *SEL*
              
              $text_title = "Část odstraněna." ;
              $text_body = "<h1>Část byla odstraněna ze seznamu částí</h1><p>... vyčkejte, prosím </p><br><br>";
              
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
        
        //test unikatnosti zadaneho kodu casti, nedovolit duplicitni kod
//        if (!$this->cast_id->Get_value()) { //pro novy
//            //echo "<br> Je to nová část.";
//            // echo "<br>" .  $this->cast_kod->Get_value() ;
//            
//            $sql_query = "SELECT * FROM cast_kurzu WHERE trim(cast_kod)= '" .  trim($this->cast_kod->Get_value()) ."' and not deleted";
//            //echo  "<br>"  . $sql_query ;
//            $res=$this->mdb2->query($sql_query);
//            //echo "<br> Počet kolikrat je kod uz pouzit: " . $res->rowCount();
//            
//            if ($res->rowCount()) {
//                $this->error_bool=true;
//                $this->cast_kod->set_error("Tento kód části již existuje!");
//            }       
//        }
//        else {         //pro update jiz existujici casti
//            $sql_query = "SELECT * FROM cast_kurzu WHERE trim(cast_kod)= '" .  trim($this->cast_kod->Get_value()) .
//                        "' and id_cast_kurzu <>" .  $this->cast_id->Get_value(). " and not deleted";
//            //echo  "<br>"  . $sql_query ;
//            $res=$this->mdb2->query($sql_query);
//            //echo "<br> Počet kolikrat je kod uz pouzit krome existujiciho: " . $res->rowCount();
//            
//            if ($res->rowCount()) {
//                $this->error_bool=true;
//                $this->cast_kod->set_error("Tento kód části již existuje!!");
//            }
//        }
        
        
        
        //test vnitřních hodnot
        if( //$this->cast_kod->Get_errors() OR
            $this->cast_nazev->Get_errors() OR
            $this->kategorie_casti->Get_errors() 
             )
        {
            $this->error_bool=true;
        }    
        
        
        //   test pro duplikat 
        if ($this->duplikat) { //echo "v Get_errors - cast";
        
//            if ((!$this->cast_nazev->byla_zmena_hodnoty() ) and (!$this->cast_kod->byla_zmena_hodnoty() ) ) {
//                 $this->error_bool=true;
//              $this->cast_nazev->set_error("Nezměnil se zároveň kód a název casti");
//              $this->cast_kod->set_error("Nezměnil se zároveň kód a název casti!");
//            }
            
             if (!$this->cast_nazev->byla_zmena_hodnoty() )  {
                 $this->error_bool=true;
                 $this->cast_nazev->set_error("Nezměnil se název části!");
             }
        
        }
        
        
        if($this->error_bool) {
            return true;
        }
        else {
            return false;
        }
    }    //function Get_errors()
    
    
    
    /**
     * $uloz_dupl=true  - pro duplikaci, ulozi INSERTem
     */
    public function Save($uloz_dupl=false) {
        //echo "<br>this v Save*<br>";
        //print_r($this);
        
        if(!$this->error_bool) {
            
            if(!$this->cast_id->Get_value() or ($uloz_dupl)) {
                try {
                    
  //----------------------- priprava noveho poradoveho cisla u insertu
                $sql_query = 
                        'SELECT if(isnull( MAX(cast_poradove_cislo)), 0,MAX(cast_poradove_cislo)) as max from cast_kurzu
                              where cast_kurzu.id_c_kategorie_FK = 
                              (select id_c_kategorie from c_kategorie where kod=\'' . $this->kategorie_casti->Get_value() .'\')' ;                                               
                 //print_r($sql_query);
                
                   $res =$this->mdb2->query($sql_query);
                   $maxi = $res->fetch(\PDO::FETCH_ASSOC);
                   //echo "<br>****max***"  . $maxi['max']. "<br>" ;
                   
                   $this->cast_poradove_cislo = $maxi['max'] +1;
                   
                   //echo "<br>castporadovecislo* " .$this->cast_poradove_cislo . "<br>" ;
                               
                //-----------------------                    
                    
                
                $sql_query = 'INSERT INTO cast_kurzu (
                                            id_c_kategorie_FK, 
                                           
                                            cast_poradove_cislo,
                                            cast_nazev,
                                            cast_nazev_skripta)
                                    VALUES (                            
                            (SELECT id_c_kategorie FROM c_kategorie WHERE kod = '.$this->mdb2->quote($this->kategorie_casti->Get_value(),"text").'), ' .        
                           
                            $this->mdb2->quote($this->cast_poradove_cislo,"integer" ).', '  .
                            $this->mdb2->quote($this->cast_nazev->Get_value()) .', '  .
                            $this->mdb2->quote($this->cast_nazev_skripta->Get_value(),"text") 
                            .')';
                //print_r( $sql_query);
                 //.$this->mdb2->quote($this->cast_kod->Get_value()).', ' .
                }
                catch (Exception $e) {
                    print_r($e);
                }
                
                $res=$this->mdb2->exec($sql_query);
                
                $this->cast_id = new id("cast_id",$this->mdb2->lastInsertID());
                
                //echo "Insertuji";  // *SEL*
            }
            
            else {
                
                
   //echo "<br>kategorie casti kod* " .$this->kategorie_casti->Get_value() ;
   if ($this->kategorie_casti->byla_zmena_hodnoty()== true) {
     // echo "<br>Byla zmena hodnoty-kategorie.<br>";
              //----------------------- priprava noveho poradoveho cisla u update
                $sql_query = 
                            'SELECT if(isnull( MAX(cast_poradove_cislo)), 0,MAX(cast_poradove_cislo)) as max from cast_kurzu
                             where cast_kurzu.id_c_kategorie_FK = 
                             (select id_c_kategorie from c_kategorie where kod=\'' . $this->kategorie_casti->Get_value() .'\')' ;   
                //print_r($sql_query);
                //exit;
                   $res =$this->mdb2->query($sql_query);
                   $maxi = $res->fetch(\PDO::FETCH_ASSOC);
                  // echo "<br>****max***"  . $maxi['max']. "<br>" ; 
                   
                   $this->cast_poradove_cislo= $maxi['max']+1; 
                   
                   //echo "<br>castporadovecislo* " .$this->cast_poradove_cislo . "<br>" ;
      } 
   else {
      //echo "<br>NEByla zmena hodnoty-kategorie.<br>";
       }                
   
                
               //-------------------------------------------- 
                
 
                $sql_query = 'UPDATE cast_kurzu
                                SET
                    id_c_kategorie_FK = (SELECT id_c_kategorie FROM c_kategorie WHERE kod = '.$this->mdb2->quote($this->kategorie_casti->Get_value(),"text").'),         
                    
                    cast_poradove_cislo =     '.$this->mdb2->quote( $this->cast_poradove_cislo,"integer" ).', 
                    cast_nazev = '.$this->mdb2->quote($this->cast_nazev->Get_value()).',
                    cast_nazev_skripta = '.$this->mdb2->quote($this->cast_nazev_skripta->Get_value(),"text").
                    ' WHERE id_cast_kurzu ="'.$this->cast_id->Get_value().'"';
                //print_r($sql_query);
                //cast_kod = '.$this->mdb2->quote($this->cast_kod->Get_value()).',
                $res=$this->mdb2->exec($sql_query);
                
                //echo "Updatuji"; // *SEL*
            }
            /*****************************************************/
           
            
            /*****/ //     echo ( "<br>**this-moduly casti v Save-value* " .  implode(',',$this->moduly_casti->Get_value()) . "<br>") ;   // *SEL* 
              //print_r ($this->moduly_casti->Get_value());
            
                      
            //vymazu vsechny navazane moduly k casti
            $sql_query ='DELETE FROM vzb_cast_modul_kurzu
                                WHERE id_cast_kurzu_FK ='.$this->cast_id->Get_value(); 
                                  
            $res=$this->mdb2->exec($sql_query);
            //echo "<br>" . $sql_query ."<br>" ;
            
            if ( count($this->moduly_casti->Get_value())) {
                
                foreach  ($this->moduly_casti->Get_value() as $klic=>$hodn) {
                
                  $sql_query  = 'INSERT INTO vzb_cast_modul_kurzu
                        (id_cast_kurzu_FK, id_modul_kurzu_FK,razeni) VALUES
                        (' . $this->cast_id->Get_value() . ',' . $hodn . ',' . $klic . ')';                                    
                  //echo "<br>ukladaci*" . $sql_query ."<br>" ;
                  $res=$this->mdb2->exec($sql_query);           
                }              
            }
          
                
                
       
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">';
        echo '<html>';
        echo '<head>';
        echo '<meta http-equiv="refresh" content="4;url='.$this->action_script.'">';
        echo '<meta http-equiv="Content-Type" CONTENT="text/html; charset=utf-8">';
        echo '<meta http-equiv="Content-Language" content="cs">';
        echo '<title>Změny uloženy</title>';
        echo '</head>';
        echo '<body>';
        echo '<h3>Vaše změny byly v pořádku uloženy</h3>';
        echo '<p>... vyčkejte, prosím </p><br><br>';
        echo '</body>';
        echo '</html>';
        }
    }
    
} //class f_casti_kurzu_cast


?>
