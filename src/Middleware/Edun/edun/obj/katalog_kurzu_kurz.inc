<?php
/**
 * Object depository for katalog_kurzu.php
 * 
 * @name katalog_kurzu_kurz.inc
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


     
class f_katalog_kurzu_kurz extends webform {
    private $error_bool=false;
    private $mdb2;
    private $kurz_id;
    private $action_script;
    private $kurz_kod; 
    private $kurz_poradove_cislo;
    private $druh_kurzu_kod;
    private $kategorie_kurzu_kod;
    private $pocet_ucastniku_min;
    private $pocet_ucastniku_max;
    private $delka_hodin;
    private $delka_detail;
    private $delka_prubeh;
    //private $cena;
    //private $cena_detail;
    private $kurz_Nazev;
    private $kurz_Anotace;
    private $kurz_Cile;
    private $kurz_Obsah;
    private $kurz_Vstupni_znalosti;
    private $kurz_Doporuceni;
    private $cilova_skupina;
    //private $anotace;
    private $dalsi_kurzy;
    private $kontakt;
    
   // private $moduly_kurzu;
   // private $moduly_kurzu_obsahy;  // retezec html, neni prvkem formulare(neposila se v POST) 
    
    private $edun_prechod_item_id_tam;  //pro prechod na edititko modulu  
    
    private $casti_kurzu;
    private $casti_kurzu_obsahy;  // retezec html, neni prvkem formulare(neposila se v POST) 
    
    private $zobrazeni_obsahu;    
    
         
    /*private $cast_kurzu_dalsi_nazev;*/
    
    private $duplikat;
    
    
    /**
     *Constructor
     *
     *@param string $name Název, pod kterým bude formulář vložen na stránku
     *@param string $action_script Skript, který bude zpracovávat data z formuláře
     *@param $kurz_id
     *@param boolean $duplikace true=konstruuji duplikat, bude se jinak ukládat
     *
     * @access  public
     */
    
    public function __construct($name,$action_script,$kurz_id=false, $duplikace=false) {
        //echo "<br><br>-----------kurz-construct------------<br>";  // *SEL* 
        $this->mdb2 = Middleware\Edun\AppContext::getDb();
        $this->action_script=$action_script;
        if ($duplikace) {
              $this->duplikat=true;}
        else {$this->duplikat=false;}
        
        parent::__construct($name,$this->action_script);
                          
        if($kurz_id) {
                        /*   vyber vseho o kurzu , co zobrazim   */
            $sql_query = 'SELECT
                    (SELECT kod FROM c_druh_kurzu WHERE c_druh_kurzu.id_c_druh_kurzu = kurz.id_c_druh_kurzu_FK) AS druh_kurzu,
                    (SELECT kod FROM c_kategorie WHERE c_kategorie.id_c_kategorie = kurz.id_c_kategorie_FK) AS kategorie_kurzu,
                    id_popis_kurzu_FK,
                    zobrazeni_obsahu,
                    kurz_poradove_cislo,
                    kod_kurzu,
                    kurz_nazev_skripta,
                    pocet_ucastniku_min,
                    pocet_ucastniku_max,
                    delka_hodin,
                    delka_detail,
                    (SELECT kod FROM c_delka_prubeh WHERE c_delka_prubeh.id_c_delka_prubeh = kurz.id_c_delka_prubeh_FK) AS delka_prubeh,
                    
                    (SELECT kod FROM c_kontakt WHERE c_kontakt.id_c_kontakt = kurz.id_c_kontakt_FK) AS kontakt,
                    inserted,
                    updated
                FROM kurz
                WHERE id_kurz = '.$kurz_id.';';
       //cena,
       //cena_detail,     
          
            
            $res=$this->mdb2->query($sql_query);
            
            $kurz = $res->fetch(\PDO::FETCH_ASSOC);
               //echo "<br>KURz **"; print_r($kurz); // /*SEL*/
            if($kurz['updated']) {
                $sql_date=$kurz['updated'];
            }
            else {
                $sql_date=$kurz['inserted'];
            }
           
            $this->kurz_id= new id("kurz_id",$kurz_id,$sql_date);    /*objekt  id*/
            
            /*****************************************/
            $sql_query = 'SELECT
                    kurz_Nazev,
                    kurz_Anotace,
                    kurz_Cile,
                    kurz_Obsah,
                    kurz_Vstupni_znalosti,
                    kurz_Doporuceni
                FROM popis_kurzu
                WHERE id_popis_kurzu = '.$kurz['id_popis_kurzu_FK'].';';
             //echo "<br>*f_katalog_kurzu_kurz* " . $sql_query;  // *SEL*
            $res=$this->mdb2->query($sql_query);
 //echo "<br>*f_katalog_kurzu_kurz* " . $sql_query;  // *SEL*
            $popis_kurzu = $res->fetch(\PDO::FETCH_ASSOC);
            //print_r($popis_kurzu);
            /************************************/
            
            $sql_query = 'SELECT
                            kod
                            FROM vzb_kurz_cilova_skupina INNER JOIN c_cilova_skupina
                            ON vzb_kurz_cilova_skupina.id_c_cilova_skupina_FK = c_cilova_skupina.id_c_cilova_skupina
                            WHERE id_kurz_FK = '.$this->kurz_id->Get_value().';';
            
             //echo "<br>*vzb cilova* " . $sql_query;  // *SEL*
            $res=$this->mdb2->query($sql_query);
             //echo "<br>*vzb cilova* " . $sql_query;  // *SEL*
            $cilova_skupina = array();
            while($cilova_skupina_polozka = $res->fetch(\PDO::FETCH_ASSOC)) {
                array_push($cilova_skupina,$cilova_skupina_polozka['kod']);
            }
            //print_r($cilova_skupina);
            //****************************************
            
            $sql_query = 'SELECT
                            vzb_navazujici_kurzy.id_kurz_navazujici_FK, poradi_navazujicich, kurz.deleted        
                            FROM vzb_navazujici_kurzy
                            left join kurz on kurz.id_kurz = vzb_navazujici_kurzy.id_kurz_navazujici_FK 
                            WHERE id_kurz_FK = '.$this->kurz_id->Get_value() .
                            ' and (kurz.deleted=0) ' .
                            ' order by poradi_navazujicich';
//echo "<br>*vzb* " . $sql_query;  // *SEL*
            $res=$this->mdb2->query($sql_query);
              //echo "<br>*vzb* " . $sql_query;  // *SEL*
            $navazujici_kurzy = array();
            while($navazujici_kurzy_polozka = $res->fetch(\PDO::FETCH_ASSOC)) {
                array_push($navazujici_kurzy,$navazujici_kurzy_polozka['id_kurz_navazujici_fk']);
            }
            //echo "AAA" ;print_r($navazujici_kurzy);   // *SEL*
            //****************************************
         
            
 // zobrazeni obsahu casti***
            // vybrat  casti kurzu --  a z nich  moduly a z modulu kurzu obsahy - naskladat do casti_kurzu_obsahy  
            
            $sql_query = 'SELECT
                            vzb_kurz_cast_kurzu.id_kurz_FK, vzb_kurz_cast_kurzu.id_cast_kurzu_FK , cast_kurzu.cast_nazev, cast_kurzu.deleted           
                            FROM vzb_kurz_cast_kurzu 
                            left join cast_kurzu on  vzb_kurz_cast_kurzu.id_cast_kurzu_FK = cast_kurzu.id_cast_kurzu 
                            WHERE (vzb_kurz_cast_kurzu.id_kurz_FK = '.$this->kurz_id->Get_value()  . ')' .
                            ' and (cast_kurzu.deleted=0) ' .
                            ' order by razeni'. ';';
              //echo ("<br>" . $sql_query . "<br>");   // *SEL*
            $res=$this->mdb2->query($sql_query);
              //echo "<br>*vzb* " . $sql_query;  // *SEL*  
            
            $casti_kurzu = array(); 
            $casti_kurzu_nazvy = array();
            
            while($casti_kurzu_polozka = $res->fetch(\PDO::FETCH_ASSOC)) {
                array_push($casti_kurzu,$casti_kurzu_polozka['id_cast_kurzu_FK']); //zjisti id vsech casti 
                array_push($casti_kurzu_nazvy,$casti_kurzu_polozka['cast_nazev']);
            }
            //casti jen nevymazane
            //echo "<br>***v constructu kurz - Scasti_kurzu*** pole <br>";    //    *SEL*
            //print_r($casti_kurzu); echo '<br>';
            //print_r($casti_kurzu_nazvy); echo '<br>';
            //---
            
            
            
            $casti_kurzu_obsahy = '';
            foreach  ($casti_kurzu as $klic=>$hodn)  //pro kazdou cast(nevymazanou)  hledam jeji moduly (nevymazane)
            {
//                  $sql_query_1 = 'select *
//                                  
//                                  from  vzb_cast_modul_kurzu 
//                                  left join cast_kurzu on  vzb_cast_modul_kurzu.id_cast_kurzu_FK = cast_kurzu.id_cast_kurzu 
//                                  left join  modul_kurzu on  vzb_cast_modul_kurzu.id_modul_kurzu_FK =  modul_kurzu.id_modul_kurzu 
//                                
//                                  where 
//                                  (vzb_cast_modul_kurzu.id_cast_kurzu_FK = ' . $hodn . ')' .
//                                  ' and (cast_kurzu.deleted=0) and (modul_kurzu.deleted=0) ' .
//                                  ' order by vzb_cast_modul_kurzu.razeni';
//                 
                  
                  
                $sql_query_1 = 'select 
                        id_cast_kurzu_fk,
                        id_modul_kurzu_fk,
                        M.id_modul_kurzu as m_id, 
                        M.inserted as m_inserted, M.updated as m_updated,
                        modul_poradove_cislo, modul_kod, modul_nazev, modul_obsah,            
                        CKAT.kod as kod_kategorie_casti,
                        MKAT.kod as kod_kategorie_modulu                
                        from  vzb_cast_modul_kurzu 
                          left join cast_kurzu  as C on  vzb_cast_modul_kurzu.id_cast_kurzu_FK = C.id_cast_kurzu 
                          left join  modul_kurzu as M on  vzb_cast_modul_kurzu.id_modul_kurzu_FK =  M.id_modul_kurzu 
                          left join c_kategorie as CKAT on C.id_c_kategorie_FK = CKAT.id_c_kategorie 
                          left join c_kategorie as MKAT on M.id_c_kategorie_FK = MKAT.id_c_kategorie                     
                        where 
                            (vzb_cast_modul_kurzu.id_cast_kurzu_FK =' . $hodn . ')' .
                            ' and (C.deleted=0) and (M.deleted=0) ' .
                            ' order by vzb_cast_modul_kurzu.razeni';
                   //echo ("<br>" . $sql_query_1. "<br>");  
                  
                  $res_1 =$this->mdb2->query($sql_query_1);
                                   
                  
                  $casti_kurzu_obsahy .= "<DIV " . webform_styles::$formular_cast  . " >";
                  $casti_kurzu_obsahy .= "<DIV id=div_cast_nazev " . webform_styles::$formular_cast_nazev  . " >";
                  $casti_kurzu_obsahy .= '<b>' . $casti_kurzu_nazvy[$klic] . '</b>';
                  $casti_kurzu_obsahy .= "</DIV>"; 
                  
                  $cast_kurzu_obsah='';
                  while($modul_kurzu_polozka = $res_1->fetch(\PDO::FETCH_ASSOC)) {
                      
                          
                          if($modul_kurzu_polozka['m_updated']) {
                                $sql_date_modulu = $modul_kurzu_polozka['m_updated'];
                          }
                          else {
                                 $sql_date_modulu = $modul_kurzu_polozka['m_inserted'];
                          }                     
                           //print_r($sql_date_modulu);
                           
                           $pripona  =  "_" .  str_pad($modul_kurzu_polozka['id_cast_kurzu_fk'],4, "0", STR_PAD_LEFT) .
                             "_" .  str_pad($modul_kurzu_polozka['id_modul_kurzu_fk'],4, "0", STR_PAD_LEFT) ;
                           
                           $cast_kurzu_obsah .=
                             "<DIV id='div_modul_nazev_jeden" . $pripona .
                             "' " .webform_styles::$formular_kurz_modul_nazev  .      
                             " >--modul--" .   $modul_kurzu_polozka['kod_kategorie_modulu'] .
                                   $modul_kurzu_polozka['modul_poradove_cislo'] .
                                   " (kod:" . $modul_kurzu_polozka['modul_kod'] . ")  " .  $modul_kurzu_polozka['modul_nazev'] . "&nbsp;&nbsp;" .
                                            //$pripona .  
                             "<input type='button' name='button_obsah_modulu". $pripona . "' " . 
                             " id='button_obsah_modulu". $pripona . "' " . 
                             " value='Zobraz obsah' " ." onClick=\"zobraz_skryj_div_tlacitkem(" .     
                             "'div_modul_obsah_jeden" . $pripona . "'," .
                             "'button_obsah_modulu". $pripona . "'," .  "'obsah'" .      
                             ");\" "  .  
                             webform_styles::$drobny_butt   .  "> " .

          //"*** *" . $modul_kurzu_polozka['m_id'] . " " .id_code::code($modul_kurzu_polozka['m_id'],$sql_date).                                
              "&nbsp;&nbsp;&nbsp;
               <a href=\"javascript: submit_edit_prechod('". id_code::code($modul_kurzu_polozka['m_id'],$sql_date_modulu).   
                                                   "','"    . id_code::code($kurz_id, $sql_date) .     "')" .                                                    
              "\"> <img src='img/b_edit.png' alt='p' title='Přejdi na modul' border='0'></a>" .                                                                      
                                
                             "</DIV> " .
                                   
                             "<DIV id='div_modul_obsah_jeden" . $pripona . 
                             "' " .webform_styles::$formular_kurz_modul_obsah  .       
                             " >" . $modul_kurzu_polozka['modul_obsah'] . "</DIV>" ;
                  } 
                  
                  $casti_kurzu_obsahy .=$cast_kurzu_obsah;
                  
                  $casti_kurzu_obsahy .= "</DIV>";        
                 
            }            
            
            //echo "<br> v constructu kurz - casti_kurzu_obsahy <br>";
            // print_r($casti_kurzu_obsahy);     //  *SEL*   ladici
            /*****************************************/

$this->edun_prechod_item_id_tam = new id("edun_prechod_item_id_tam");   //priprava pro  prechod na edititko modulu
$this->edun_prechod_item_id_zpet = new id("edun_prechod_item_id_zpet"); 
$this->edun_prechod = "";                      
            
        }
        else {
            $this->kurz_id = new id("kurz_id");            /*objekt  id*/
            
            //vytvorit nove (dalsi v poradi) poradove cislo kurzu - az pri zapisu do tabulky!!!!!!            
        }
    
         /*  vytvorit objekty jednotlivych prvku formulare */
        $popisy_castecne=array("chci zobrazit obsah kurzu"," chci zobrazit obsahy z modulů","chci zobrazit obsah kurzu i obsahy z modulů");
        $this->zobrazeni_obsahu = new c_webform_radio("zobrazeni_obsahu","Zde si vyberte, který obsah se bude zobrazovat (používat):",$popisy_castecne,3,@$kurz['zobrazeni_obsahu']);
        $this->zobrazeni_obsahu->Set_default("2"); //2-bude videt div_obsah_casti, 1-bude videt div_obsah_kurz 3-budou videt oba divy
         
        // $this->kurz_kod = new c_webform_input_text("kod","Kód kurzu:",10,2,10,@$kurz['kod_kurzu']);
        $this->kurz_kod = @$kurz['kod_kurzu'];
        $this->kurz_poradove_cislo = @$kurz['kurz_poradove_cislo'];
            
        $this->kurz_Nazev = new c_webform_input_text("kurz_Nazev","Název kurzu:",50,5,200,@$popis_kurzu['kurz_nazev']); 
        
        $this->kurz_nazev_skripta = new c_webform_textarea("kurz_nazev_skripta","Název kurzu ve skriptech:",90,2,0,500,@$kurz['kurz_nazev_skripta']);
        $this->druh_kurzu_kod = new c_webform_cselect("druh_kurzu_kod","Druh kurzu:",1,"c_druh_kurzu",@$kurz['druh_kurzu']);
        
        $this->kategorie_kurzu_kod = new c_webform_cselect("kategorie_kurzu_kod","Kategorie kurzu:",1,"c_kategorie",@$kurz['kategorie_kurzu']);
        
        //echo "<br>MIN<br><br><br><br><br>" .$kurz['pocet_ucastniku_min'] ."<br>";  // / *SEL* /
        $this->pocet_ucastniku_min = new c_webform_input_text("pocet_ucastniku_min","Počet účastníků od:",5,0,5,@$kurz['pocet_ucastniku_min']);
        $this->pocet_ucastniku_max = new c_webform_input_text("pocet_ucastniku_max"," do:",5,1,5,@$kurz['pocet_ucastniku_max']);
        $this->delka_hodin = new c_webform_input_text("delka_hodin","Délka kurzu v hodinách:",5,1,5,@$kurz['delka_hodin']);
        $this->delka_detail = new c_webform_input_text("delka_detail","Upřesnění průběhu kurzu:",80,0,100,@$kurz['delka_detail']);
        $this->delka_prubeh = new c_webform_cselect("delka_prubeh","Průběh kurzu:",1,"c_delka_prubeh",@$kurz['delka_prubeh']);
       // $this->cena = new c_webform_input_text("cena","Cena kurzu pro účastníka:",5,0,10,@$kurz['cena']);
       // $this->cena_detail = new c_webform_input_text("cena_detail","Bližší informace k ceně:",80,0,100,@$kurz['cena_detail']); 
        $this->cilova_skupina = new c_webform_input_checkbox("cilova_skupina","Doporučeno pro:",4,"c_cilova_skupina",@$cilova_skupina);
        $this->kurz_Anotace = new c_webform_htmlarea("anotace","Anotace kurzu:",90,15,10,800,@$popis_kurzu['kurz_anotace']);
	$this->kurz_Anotace->Set_default("Zadejte stručnou anotaci tohoto kurzu");
            
        $this->kurz_Cile = new c_webform_htmlarea("cile","Cíle kurzu:",90,20,10,800,@$popis_kurzu['kurz_cile']);
        $this->kurz_Obsah = new c_webform_htmlarea("obsah","Obsah kurzu:",90,40,0,4000,@$popis_kurzu['kurz_obsah']);
        
              
//-----------tady  CASTI-------
        $this->casti_kurzu = new c_webform_cselect_multi("casti_kurzu",
                                     "Části kurzu a jejich obsah z modulů:<BR>"  ,
                                     1,"view_c_casti",@$casti_kurzu);
        
        $this->casti_kurzu_obsahy= $casti_kurzu_obsahy;  //nebude v POST
           //echo ( "<br><br>** this-casti kurzu v constructu kurz-old value* " .  implode(',',$this->casti_kurzu->Get_old_value()) . "<br>") ;  // *SEL* 
          
             
        $this->kurz_Vstupni_znalosti = new c_webform_htmlarea("znalosti","Doporučené vstupní znalosti účastníka:",90,15,0,500,@$popis_kurzu['kurz_vstupni_znalosti']);
        $this->kurz_Doporuceni = new c_webform_htmlarea("doporuceni","Další doporučení ke kurzu:",90,15,0,500,@$popis_kurzu['kurz_doporuceni']);
        $this->dalsi_kurzy = new c_webform_cselect_multi("dalsi_kurzy",
                                     "Další doporučené kurzy:<BR>"  ,
                                     1,"view_c_kurzy",@$navazujici_kurzy);
        $this->kontakt = new c_webform_cselect("kontakt","Kontakt pro další informace:",1,"c_kontakt",@$kurz['kontakt']);
        
    } //function __construct
    
    
    
   public function Display($part=NULL) {
        parent::Display('top');
   
              //    echo ( "<br>//*this-casti kurzu v display*" .  implode(',',$this->casti_kurzu->Get_value()) . "//<br>") ;  // *SEL* 
              //print_r ($this->casti_kurzu->Get_value());
              //echo "<br>";
              
              //    echo ( "<br>//*this-casti kurzu v display old value*" .  implode(',',$this->casti_kurzu->Get_old_value()) . "//<br>") ;  // *SEL* 
              //print_r ($this->casti_kurzu->Get_old_value());
              //echo "<br>";
              //echo "<hr>";
        
   //echo '*pole navrat_script*hidden:';
   echo '<input type="hidden" name="navrat_script" id="navrat_script" size="100" value="'.       
             $this->action_script .
        '"  readonly>';
   //echo "<br>";      
   
   if (!($this->duplikat))      {     
     //echo '*formularove pole action*hidden:';
     echo '<input type="hidden" name="action" id="action" value="form"  readonly>'. "\n";
     //echo "<br>"; 
     // echo '<input type="hidden"/"text" name="action" id="action" value="form">'."\n";
   }
   else {
     //echo '*formularove pole action*hidden:';
     echo '<input type="hidden" name="action" id="action" value="form_dupl"  readonly>'."\n";
     //echo "<br>"; 
     //echo '<input type="hidden" name="action" id="action" value="form_dupl">'."\n";              
   }  
        
       $this->kurz_id->Display();
        //echo "<BR><BR>\n";
       
if (isset($this->edun_prechod_item_id_tam)) {     
    $this->edun_prechod_item_id_tam->Display();    //pro prechod na edititko modulu
}
if (isset($this->edun_prechod_item_id_zpet)) {
    $this->edun_prechod_item_id_zpet->Display();  
}
if (isset($this->edun_prechod)) {    //pro prechod na edititko modulu 
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
            echo '<h3>Kurz</h3>';
        }
        else {
            echo '<h3>Kurz - duplikace</h3>'; 
            echo '<p ' .  webform_styles::$pokyn . '>Při duplikaci kurzu je nutné změnit tyto údaje -  <b>Název kurzu</b>.
                  </p>';
        }
        
        
        //$this->kurz_kod->Display();
        echo "Kód kurzu:&nbsp;&nbsp;&nbsp;" . $this->kurz_kod;
        echo "<BR><BR>\n";
        echo "Pořadové (orientační) číslo kurzu:&nbsp;&nbsp;&nbsp;" ; 
        $this->kategorie_kurzu_kod->Display('JEN_KOD'); 
        echo "<b>" . $this->kurz_poradove_cislo ."</b>";
        echo "<BR><BR>\n";
        
        $this->druh_kurzu_kod->Display();
        echo "<BR>\n";
        $this->kategorie_kurzu_kod->Display();
        echo "<BR><br>\n";  
   
        
        $this->kurz_Nazev->Display();
        echo "<BR><BR>\n";
        
        $this->kurz_nazev_skripta->Display();
        echo "<BR><BR>\n";
            
        $this->cilova_skupina->Display();
        echo "<BR><BR>\n";
        $this->kurz_Anotace->Display(); 
        echo "<BR><BR>\n";
        $this->kurz_Cile->Display();
        echo "<BR><BR>\n";
        
        $this->zobrazeni_obsahu->Display();  //2-bude videt div_obsah_casti, 1-bude videt div_obsah_kurz, 3-bude videt oba divy
        echo "<BR><BR>\n";
        
        echo "<div id=div_obsah_kurz ";
        if (($this->zobrazeni_obsahu->get_value()==1) or ($this->zobrazeni_obsahu->get_value()==3))
            //{echo " style='display:block'";} else {echo " style='display:none'";}
            {echo webform_styles::$display_block; } else {echo webform_styles::$display_none; }
        echo">";
          $this->kurz_Obsah->Display();
          echo "<BR><BR>\n";
        echo "</div>";  
        

        
        
        //----------------------casti
     
                
        echo "<div id=div_obsah_casti ";
        if (($this->zobrazeni_obsahu->get_value()==2) or ($this->zobrazeni_obsahu->get_value()==3))
            //{echo " style='display:block'";} else {echo " style='display:none'";}
            {echo webform_styles::$display_block; } else {echo webform_styles::$display_none; }
        echo">";
        
           $this->casti_kurzu->Display();
         //echo "<BR><BR>\n";
           echo "<div " .  webform_styles::$formular_text   .  ">" . $this->casti_kurzu_obsahy . "</div>" ;
           echo "<BR>";
       
        echo "</div>";
        
       
      
        //-----------------------------------------
        
        //----------------------moduly  obsahy
 //       echo "<div id=div_obsah_moduly ";
//        if (($this->zobrazeni_obsahu->get_value()==2) or ($this->zobrazeni_obsahu->get_value()==3))
//            //{echo " style='display:block'";} else {echo " style='display:none'";}
//             {echo webform_styles::$display_block; } else {echo webform_styles::$display_none; }
 //       echo">";
//          $this->moduly_kurzu->Display();
//          //echo "<BR><BR>\n";
//          echo "<div " .  webform_styles::$formular_text   .  ">" . $this->moduly_kurzu_obsahy . "</div>" ;
//          echo "<BR>\n";
  //      echo "</div>"; 
         
        
        $this->pocet_ucastniku_min->Display();
        //echo "<BR><BR>\n";
        $this->pocet_ucastniku_max->Display();
        echo "<BR><BR>\n";
        $this->delka_hodin->Display();
        echo "<BR><BR>\n";
        $this->delka_detail->Display();
        echo "<BR><BR>\n";
        $this->delka_prubeh->Display();
        echo "<BR><BR>\n";
        //$this->cena->Display();
        //echo "<BR><BR>\n";
        //$this->cena_detail->Display();
        //echo "<BR><BR>\n";
        $this->kontakt->Display();
        echo "<BR><BR>\n";
        
        $this->kurz_Vstupni_znalosti->Display();
        echo "<BR><BR>\n";
        $this->kurz_Doporuceni->Display();
        echo "<BR><BR>\n";
        $this->dalsi_kurzy->Display();
        echo "<BR><BR><BR><BR>\n";
       
            
       
        parent::Display('buttons');
        parent::Display('bottom');
        
    } //Display
    
    
    public function Drop() {
        if($this->kurz_id) {
            $sql_query = 'UPDATE popis_kurzu
                            SET deleted="1"
                            WHERE id_popis_kurzu =
                                (SELECT id_popis_kurzu_FK FROM kurz WHERE id_kurz = "'.$this->kurz_id->Get_value().'");';
            $res=$this->mdb2->exec($sql_query);
            $sql_query = 'UPDATE kurz
                            SET deleted="1"
                            WHERE id_kurz="'.$this->kurz_id->Get_value().'";';
            $res=$this->mdb2->exec($sql_query);
            echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">';
            echo '<html>';
            echo '<head>';
            echo '<meta http-equiv="refresh" content="4;url='.$this->action_script.'">';
            echo '<meta http-equiv="Content-Type" CONTENT="text/html; charset=utf-8">';
            echo '<meta http-equiv="Content-Language" content="cs">';
            echo '<title>Kurz odstraněn</title>';
            echo '</head>';
            echo '<body>';
            echo '<h1>Kurz byl odstraněn ze seznamu kurzů</h1>';
            echo '<p>... vyčkejte, prosím </p><br><br>';
            echo '</body>';
            echo '</html>';
        }
    } //Drop
    
    
    public function Get_errors() {  
        $this->error_bool=false;
        
         //test unikatnosti  zadaneho kodu kurzu, nedovolit duplicitni kod
//        if (!$this->kurz_id->Get_value()) { //pro novy
//            //echo "<br> Je to nový kurz.";
//            // echo "<br>" .  $this->kurz_kod->Get_value() ;
//            
//            $sql_query = "SELECT * FROM kurz WHERE trim(kod_kurzu)= '" .  trim($this->kurz_kod->Get_value()) ."' and not deleted";
//            //echo  "<br>"  . $sql_query ;
//            $res=$this->mdb2->query($sql_query);
//            //echo "<br> Počet kolikrat je kod uz pouzit: " . $res->rowCount();
//            
//            if ($res->rowCount()) {
//                $this->error_bool=true;
//                $this->kurz_kod->set_error("Tento kód kurzu již existuje!");
//            }       
//        }
//        else {         //pro update jiz existujiciho 
//            $sql_query = "SELECT * FROM kurz WHERE trim(kod_kurzu)= '" .  trim($this->kurz_kod->Get_value()) .
//                        "' and id_kurz <>" .  $this->kurz_id->Get_value() . " and not deleted";
//            //echo  "<br>"  . $sql_query ;
//            $res=$this->mdb2->query($sql_query);
//            //echo "<br> Počet kolikrat je kod uz pouzit krome existujiciho: " . $res->rowCount();
//            
//            if ($res->rowCount()) {
//                $this->error_bool=true;
//                $this->kurz_kod->set_error("Tento kód kurzu již existuje!!");
//            }
//        }
        

        
        //test vnitřních hodnot
        if( //$this->kurz_kod->Get_errors() OR
            $this->kurz_Nazev->Get_errors() OR
            $this->kurz_nazev_skripta->Get_errors() OR
            $this->druh_kurzu_kod->Get_errors() OR
            $this->kategorie_kurzu_kod->Get_errors() OR
            $this->kurz_Anotace->Get_errors() OR
            $this->kurz_Cile->Get_errors() OR
            $this->kurz_Obsah->Get_errors() OR
            $this->pocet_ucastniku_min->Get_errors() OR
            $this->pocet_ucastniku_max->Get_errors() OR
            $this->delka_hodin->Get_errors() OR
            $this->delka_detail->Get_errors() OR
            $this->delka_prubeh->Get_errors() OR
            //$this->cena->Get_errors() OR
            //$this->cena_detail->Get_errors() OR
            $this->kontakt->Get_errors() OR
            $this->cilova_skupina->Get_errors() OR
            $this->kurz_Vstupni_znalosti->Get_errors() OR
            $this->dalsi_kurzy->Get_errors() OR
            $this->kurz_Doporuceni->Get_errors())
        {
            $this->error_bool=true;
        }
        
        // kontrola počtu osob
        if($this->pocet_ucastniku_max->get_value() < $this->pocet_ucastniku_min->get_value() ) {
            $this->error_bool=true;
            $this->pocet_ucastniku_max->set_error("Minimální počet účastníků musí být menší než maximální počet účastníků!");
        }
        
        
        if ($this->duplikat) { //echo "v Get_errors - kurz";
        
//            if ((!$this->kurz_Nazev->byla_zmena_hodnoty() ) and (!$this->kurz_kod->byla_zmena_hodnoty() ) ) {
//                 $this->error_bool=true;
//              $this->kurz_Nazev->set_error("Nezměnil se zároveň kód a název kurzu!");
//              $this->kurz_kod->set_error("Nezměnil se zároveň kód a název kurzu!");
//            }
            
             if (!$this->kurz_Nazev->byla_zmena_hodnoty() )  {
                 $this->error_bool=true;
                 $this->kurz_Nazev->set_error("Nezměnil se název kurzu!");
             }
        
        }
        
        
        if($this->error_bool) {
            return true;
        }
        else {
            return false;
        }
    }  //function Get_errors
 
    
    
    /**
     * $uloz_dupl=true  - pro duplikaci, ulozi INSERTem
     */
    public function Save($uloz_dupl=false) {
    //echo "<br><br>----------------------kurz-Save------------------<br>";   // *SEL*   
          //    echo ( "<br>/" .  implode(',',$this->moduly_kurzu->Get_value()) . "/<br>") ;
          //    print_r ($this->moduly_kurzu->Get_value());
           
        if(!$this->error_bool) {
            
            if ((!$this->kurz_id->Get_value()) or ($uloz_dupl)) {
                try {
                $sql_query = 'INSERT INTO popis_kurzu (
                                                kurz_Nazev,
                                                kurz_Anotace,
                                                kurz_Cile,
                                                kurz_Obsah,
                                                kurz_Vstupni_znalosti,
                                                kurz_Doporuceni)
                                    VALUES(
                            '.$this->mdb2->quote($this->kurz_Nazev->Get_value(),"text").',
                            '.$this->mdb2->quote($this->kurz_Anotace->Get_value(),"text").',
                            '.$this->mdb2->quote($this->kurz_Cile->Get_value(),"text").',
                            '.$this->mdb2->quote($this->kurz_Obsah->Get_value(),"text").',
                            '.$this->mdb2->quote($this->kurz_Vstupni_znalosti->Get_value(),"text").',
                            '.$this->mdb2->quote($this->kurz_Doporuceni->Get_value(),"text").'
                                            )';
                                            
                }
                catch (Exception $e) {
                    print_r($e);
                }
                                            
                $res=$this->mdb2->exec($sql_query);
                $id_popis_kurzu = $this->mdb2->lastInsertID();
                   
   ///***      //----------------------- priprava noveho poradoveho cisla u insertu
                $sql_query = 
                        'SELECT if(isnull( MAX(kurz_poradove_cislo)), 0,MAX(kurz_poradove_cislo)) as max from kurz
                              where kurz.id_c_kategorie_FK = 
                              (select id_c_kategorie from c_kategorie where kod=\'' . $this->kategorie_kurzu_kod->Get_value() .'\')' ;                                               
                 //print_r($sql_query);
                
                   $res =$this->mdb2->query($sql_query);
                   $maxi = $res->fetch(\PDO::FETCH_ASSOC);
                   //echo "<br>****max***"  . $maxi['max']. "<br>" ;
                   
                   $this->kurz_poradove_cislo = $maxi['max'] +1;
                   
                   //echo "<br>kurzporadovecislo* " .$this->kurz_poradove_cislo . "<br>" ;
                             
                //-----------------------
                
                $sql_query = 'INSERT INTO kurz (
                                            id_c_druh_kurzu_FK,
                                            id_c_kategorie_FK,
                                            id_popis_kurzu_FK,
                                            zobrazeni_obsahu,
                                        
                                       kurz_poradove_cislo,
                                            kurz_nazev_skripta,
                                            pocet_ucastniku_min,
                                            pocet_ucastniku_max,
                                            id_c_delka_prubeh_FK,
                                            delka_hodin,
                                            delka_detail,
                                            
                                            id_c_kontakt_FK)
                                    VALUES (
                            (SELECT id_c_druh_kurzu FROM c_druh_kurzu WHERE kod = '.$this->mdb2->quote($this->druh_kurzu_kod->Get_value(),"text").'),
                            (SELECT id_c_kategorie FROM c_kategorie WHERE kod = '.$this->mdb2->quote($this->kategorie_kurzu_kod->Get_value(),"text").'),
                            '.$this->mdb2->quote($id_popis_kurzu,"integer").',
                            '.$this->mdb2->quote($this->zobrazeni_obsahu->Get_value(),"integer").',
                       
        '.$this->mdb2->quote( $this->kurz_poradove_cislo,"integer" ).',
                
                            '.$this->mdb2->quote($this->kurz_nazev_skripta->Get_value(),"text").',    
                            '.$this->mdb2->quote($this->pocet_ucastniku_min->Get_value(),"integer").',
                            '.$this->mdb2->quote($this->pocet_ucastniku_max->Get_value(),"integer").',
                            (SELECT id_c_delka_prubeh FROM c_delka_prubeh WHERE kod = '.$this->mdb2->quote($this->delka_prubeh->Get_value(),"text").'),
                            '.$this->mdb2->quote($this->delka_hodin->Get_value(),"integer").',
                            '.$this->mdb2->quote($this->delka_detail->Get_value(),"text").',
                           
                            (SELECT id_c_kontakt FROM c_kontakt WHERE kod = '.$this->mdb2->quote($this->kontakt->Get_value(),"text").'));';
  //'.$this->mdb2->quote($this->kurz_kod->Get_value()).',
  //'.$this->mdb2->quote($this->cena->Get_value(),"text").',
  //'.$this->mdb2->quote($this->cena_detail->Get_value(),"text").',        
//   '.$this->mdb2->quote( $this->kurz_poradove_cislo,"integer" ).',                
  
                //print_r( $sql_query); // *SEL*
                $res=$this->mdb2->exec($sql_query);
                $this->kurz_id = new id("kurz_id",$this->mdb2->lastInsertID());
            } //---------------------------------------------------------------------------------------------
            else {
                $sql_query =
                'UPDATE popis_kurzu   SET
                  kurz_Nazev = '.$this->mdb2->quote($this->kurz_Nazev->Get_value(),"text").',
                  kurz_Anotace = '.$this->mdb2->quote($this->kurz_Anotace->Get_value(),"text").',
                  kurz_Cile = '.$this->mdb2->quote($this->kurz_Cile->Get_value(),"text").',
                  kurz_Obsah = '.$this->mdb2->quote($this->kurz_Obsah->Get_value(),"text").',
                  kurz_Vstupni_znalosti = '.$this->mdb2->quote($this->kurz_Vstupni_znalosti->Get_value(),"text").',
                  kurz_Doporuceni = '.$this->mdb2->quote($this->kurz_Doporuceni->Get_value(),"text").'
                WHERE id_popis_kurzu = (SELECT id_popis_kurzu_FK FROM kurz WHERE id_kurz ="'.$this->kurz_id->Get_value().'");';
                
                $res=$this->mdb2->exec($sql_query);
                //print_r($res);
                
   // echo "<br>kategoriekurzu kod* " . $this->kategorie_kurzu_kod->Get_value() ;
   if ($this->kategorie_kurzu_kod->byla_zmena_hodnoty()== true) {
   // echo "<br>Byla zmena hodnoty-kategorie.<br>";
              //----------------------- priprava noveho poradoveho cisla u update
                $sql_query = 
                            'SELECT if(isnull( MAX(kurz_poradove_cislo)), 0,MAX(kurz_poradove_cislo)) as max from kurz
                             where kurz.id_c_kategorie_FK = 
                             (select id_c_kategorie from c_kategorie where kod=\'' . $this->kategorie_kurzu_kod->Get_value() .'\')' ;   
               // print_r($sql_query);
               //exit;
                   $res =$this->mdb2->query($sql_query);
                   $maxi = $res->fetch(\PDO::FETCH_ASSOC);
                  // echo "<br>****max***"  . $maxi['max']. "<br>" ; 
                   
                   $this->kurz_poradove_cislo = $maxi['max']+1; 
                   
                  //echo "<br>kurzporadovecislo* " .$this->kurz_poradove_cislo . "<br>" ;
      } 
   else {
     // echo "<br>NEByla zmena hodnoty-kategorie.<br>";
   }
        
                $sql_query = 'UPDATE kurz
                                SET
                    id_c_druh_kurzu_FK = (SELECT id_c_druh_kurzu FROM c_druh_kurzu WHERE kod = '.$this->mdb2->quote($this->druh_kurzu_kod->Get_value(),"text").'),
                    id_c_kategorie_FK = (SELECT id_c_kategorie FROM c_kategorie WHERE kod = '.$this->mdb2->quote($this->kategorie_kurzu_kod->Get_value(),"text").'),
    
                    zobrazeni_obsahu = '.$this->mdb2->quote($this->zobrazeni_obsahu->Get_value()).',
                    kurz_poradove_cislo =     '.$this->mdb2->quote( $this->kurz_poradove_cislo,"integer" ).', 
                    kurz_nazev_skripta='.$this->mdb2->quote($this->kurz_nazev_skripta->Get_value(),"text").',     
                    pocet_ucastniku_min = '.$this->mdb2->quote($this->pocet_ucastniku_min->Get_value(),"integer").',
                    pocet_ucastniku_max = '.$this->mdb2->quote($this->pocet_ucastniku_max->Get_value(),"integer").',
                    id_c_delka_prubeh_FK = (SELECT id_c_delka_prubeh FROM c_delka_prubeh WHERE kod = '.$this->mdb2->quote($this->delka_prubeh->Get_value(),"text").'),
                    delka_hodin = '.$this->mdb2->quote($this->delka_hodin->Get_value(),"integer").',
                    delka_detail = '.$this->mdb2->quote($this->delka_detail->Get_value(),"text").',
                    
                    id_c_kontakt_FK = (SELECT id_c_kontakt FROM c_kontakt WHERE kod = '.$this->mdb2->quote($this->kontakt->Get_value(),"text").')
                    WHERE id_kurz ="'.$this->kurz_id->Get_value().'";';
          // print_r($sql_query);
//kod_kurzu = '.$this->mdb2->quote($this->kurz_kod->Get_value()).',                
//cena = '.$this->mdb2->quote($this->cena->Get_value(),"text").',
//cena_detail = '.$this->mdb2->quote($this->cena_detail->Get_value(),"text").',                
     
                $res=$this->mdb2->exec($sql_query);
                //echo "Updatuji"; //*SEL*
            }
            /*****************************************************/
            //*SEL* delete nebylo = neslo odskrtnout chekcboxy. 
            
            
            $sql_query='DELETE  from vzb_kurz_cilova_skupina
                      WHERE id_kurz_FK=' . $this->kurz_id->Get_value().
                     ' AND
                      vzb_kurz_cilova_skupina.id_c_cilova_skupina_FK  NOT IN
                           (SELECT id_c_cilova_skupina  from c_cilova_skupina
                            WHERE 
                             (kod IN ("'. implode('","',$this->cilova_skupina->Get_value()) .'") )
                           ) ' ;      
            //print_r($sql_query);  
            //print_r ( "<br>cilove skupiny zaskrnute*** " .implode('","', $this->cilova_skupina->Get_value()));
                         
            $res=$this->mdb2->exec($sql_query);
            if( count($this->cilova_skupina->Get_value())) {
                $sql_query = 'INSERT INTO vzb_kurz_cilova_skupina (
                  id_kurz_FK,   id_c_cilova_skupina_FK)
                  SELECT '.
                    $this->kurz_id->Get_value(). ' as id_kurz,   id_c_cilova_skupina 
                    FROM c_cilova_skupina 
                    WHERE id_c_cilova_skupina IN
                         (SELECT id_c_cilova_skupina FROM c_cilova_skupina 
                          WHERE kod IN ("'.implode('","',$this->cilova_skupina->Get_value()).'")) 
                    AND id_c_cilova_skupina NOT IN         
                         (SELECT id_c_cilova_skupina_FK FROM vzb_kurz_cilova_skupina 
                          WHERE id_kurz_FK='.$this->kurz_id->Get_value().')' ;
                
                //print_r($sql_query);
                $res=$this->mdb2->exec($sql_query);
            }
            
            /****************************************************/
       /*     $sql_query ='DELETE FROM vzb_navazujici_kurzy 
                                WHERE id_kurz_FK ='.$this->kurz_id->Get_value().' 
                                  AND id_kurz_navazujici_FK NOT IN ("'.implode('","',$this->dalsi_kurzy->Get_value()).'");';
                                
            $res=$this->mdb2->exec($sql_query);
            if ( count($this->dalsi_kurzy->Get_value())) {
                $sql_query = 'INSERT INTO vzb_navazujici_kurzy (
                        id_kurz_FK,
                        id_kurz_navazujici_FK)
                        SELECT
                        '.$this->kurz_id->Get_value().' as id_kurz,
                        id_kurz as id_kurz_navazujici
                        FROM kurz
                        WHERE id_kurz IN ('.implode(',',$this->dalsi_kurzy->Get_value()).')
                              AND id_kurz NOT IN
                              (SELECT id_kurz_navazujici_FK FROM vzb_navazujici_kurzy WHERE id_kurz_FK='.$this->kurz_id->Get_value().');';
                                                          
                
                $res=$this->mdb2->exec($sql_query);
            }
           */
            
            //----------------------------------------------------------------
            //vymazat vsechny navazane kurzy k tomuto kurzu
            $sql_query ='DELETE FROM vzb_navazujici_kurzy 
                                WHERE id_kurz_FK ='.$this->kurz_id->Get_value();
                
            $res=$this->mdb2->exec($sql_query);
            if ( count($this->dalsi_kurzy->Get_value())) {
                foreach  ($this->dalsi_kurzy->Get_value() as $klic=>$hodn) { 
                
                    $sql_query = 'INSERT INTO vzb_navazujici_kurzy (
                        id_kurz_FK,
                        id_kurz_navazujici_FK,
                        poradi_navazujicich
                        )
                        VALUES ( ' .
                            $this->kurz_id->Get_value() .  ',' . $hodn . ',' . $klic . ')';  
                     //echo $sql_query;
                         $res=$this->mdb2->exec($sql_query);

                }
             }
                   
            
            
            
            /****************************************************/
  
            //echo ( "<br>**this-casti kurzu v Save-value* " .  implode(',',$this->casti_kurzu->Get_value()) . "<br>") ;   // *SEL* 
            //print_r ($this->casti_kurzu->Get_value());
            
            //vymazu vsechny navazane casti ke kurzu
            $sql_query ='DELETE FROM vzb_kurz_cast_kurzu
                                WHERE id_kurz_FK ='.$this->kurz_id->Get_value();                                  
            $res=$this->mdb2->exec($sql_query);
            //echo "<br>" . $sql_query ."<br>" ;
            
            if ( count($this->casti_kurzu->Get_value())) {
                
                foreach  ($this->casti_kurzu->Get_value() as $klic=>$hodn) {
                
                  $sql_query  = 'INSERT INTO vzb_kurz_cast_kurzu
                        (id_kurz_FK, id_cast_kurzu_FK,razeni) VALUES
                        (' . $this->kurz_id->Get_value() . ',' . $hodn . ',' . $klic . ')';                                    
                 // echo "<br>ukladaci*" . $sql_query ."<br>" ; // *SEL*
                  $res=$this->mdb2->exec($sql_query);         
                }              
            }
           
           
        
            // ****************************************************
            
       
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
    } //function Save
    
} //class f_katalog_kurzu_kurz




?>
