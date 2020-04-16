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



//***************************************************************************************
class f_planovane_kurzy_kurz extends webform {
    private $error_bool=false;
    private $mdb2;
    private $planovany_kurz_id;   /*id_planovane_kurzy*/
    private $action_script;
    
    //private $kurz_Nazev;
    private $kurz_planovany;
    private $pocet_ucastniku_min_plan;
    private $pocet_ucastniku_max_plan;
    private $od_data_plan;
    private $do_data_plan;
    private $od_data_skut;
    private $do_data_skut;
    
    private $misto_kurzu;
    
    private $cena_plan;
    private $cena_plan_detail;
    
    private $lektor;
    private $odberatel;
    private $kontakt;
   
    private $duplikat;
    
    /**
     *Constructor
     *
     *@param string $name Název pod kterým bude formulář vložen na stránku
     *@param string $action_script Skript, který bude zpracovávat data z formuláře
     *@param $kurz_id
     *@param boolean $duplikace true=konstruuji duplikat, bude se jinak ukládat
     *
     * @access  public
     */
    public function __construct($name,$action_script,$kurz_id=false, $duplikace=false) {
        $this->mdb2 = Middleware\Edun\AppContext::getDb();
        $this->action_script=$action_script;
        if ($duplikace) {
              $this->duplikat=true;}
        else {$this->duplikat=false;}
         
        parent::__construct($name,$this->action_script);
        
        //$kurz_id je parametr, je id z tab.planovane_kurzy
                
       if($kurz_id) {      //kurz uz existuje
                          /*   vyber vseho o kurzu , co zobrazim   */

          $sql_query = 'SELECT
           (select kod_kurzu  from  view_kurzy_prehled
             where   view_kurzy_prehled.id_kurz=planovane_kurzy.id_kurz_FK ) as kod_kurzu,
           (select  nazev_kurzu  from  view_kurzy_prehled
             where   view_kurzy_prehled.id_kurz=planovane_kurzy.id_kurz_FK)  as nazev_kurzu,
            od_data_plan, do_data_plan, od_data_skut, do_data_skut, cena_plan, cena_plan_detail,
            pocet_ucast_min_plan, pocet_ucast_max_plan,
            lektor,
            id_s_misto_kurzu_FK,
            id_s_odberatel_FK,
            (SELECT kod FROM c_kontakt WHERE c_kontakt.id_c_kontakt = planovane_kurzy.id_c_kontakt_FK) AS kontakt,
            id_kurz_FK,
            inserted,
            updated
            from planovane_kurzy
            WHERE id_planovane_kurzy = '.$kurz_id.';';
            
            //echo "<br>" ; print_r($sql_query); // *SEL* 
            
            $res=$this->mdb2->query($sql_query);
            $kurz = $res->fetch(\PDO::FETCH_ASSOC); 
            if($kurz['updated']) {
                $sql_date=$kurz['updated'];
            }
            else {
                $sql_date=$kurz['inserted'];
            }
            
            $this->planovany_kurz_id= new id("planovany_kurz_id",$kurz_id,$sql_date);    /*objekt  id*//*id planovaneho*/
            
        
       }
       else {     //kurz jeste neexistuje
           $this->planovany_kurz_id = new id("planovany_kurz_id");            /*objekt  id*/
                    //vlastnosti id a sql_date zustanou false
       }
       
    
       $sql_query = 'SELECT
            id_s_misto_kurzu as bude_upres from s_misto_kurzu
            WHERE misto_obec  = "- místo bude upřesněno"; ';
            
            //echo "<br>" ; print_r($sql_query);  // *SEL* 
            
            $res1=$this->mdb2->query($sql_query);
            $veta1 = $res1->fetch(\PDO::FETCH_ASSOC);
            //echo "BR******" .  $veta1['bude_upres']; exit;
    
       
       
       
       /*  vytvorit objekty jednotlivych prvku formulare */
            /*nepouzivat
            $this->kurz_Nazev1 = new c_webform_cselect_nazev_kurzu("nazev_kurzu","Název1 kurzu:",1,@$kurz['id_kurz']); //-idkurzu ze vsech kurzu
            */
        //$kurz['id_kurz']  je id z tab. kurz      
        $this->kurz_planovany = new c_webform_cselect("plan_kurz","Plánovaný kurz:",1,"view_c_kurzy_vse",@$kurz['id_kurz_fk']); 
       
        $this->pocet_ucastniku_min_plan = new c_webform_input_text("pocet_ucast_min_plan","Plánovaný počet účastníků od:",5,0,5,@$kurz['pocet_ucast_min_plan']);
        $this->pocet_ucastniku_max_plan = new c_webform_input_text("pocet_ucast_max_plan"," do:",5,0,5,@$kurz['pocet_ucast_max_plan']);
        $this->cena_plan =   new c_webform_input_text("cena_plan","Plánovaná cena (Kč):",10,0,10,@$kurz['cena_plan']);
        $this->cena_plan_detail = new c_webform_input_text("cena_plan_detail","Bližší informace k ceně:",80,0,200,@$kurz['cena_plan_detail']);
        
        $this->lektor = new c_webform_input_text("lektor","Předpokládaný lektor:",20,0,50,@$kurz['lektor']);
        
        $this->od_data_plan =  new c_webform_input_text("od_data_plan","Plánované datum od:",10,0,10,
                                                  Obecna_Knih::datum_ymd_dmy(@$kurz['od_data_plan']) );
        $this->do_data_plan =  new c_webform_input_text("do_data_plan"," do:",10,0,10,
                                                  Obecna_Knih::datum_ymd_dmy(@$kurz['do_data_plan']) );
        $this->od_data_skut =  new c_webform_input_text("od_data_skut","Skutečné datum od:",10,0,10,
                                                  Obecna_Knih::datum_ymd_dmy(@$kurz['od_data_skut']) );
        $this->do_data_skut =  new c_webform_input_text("do_data_skut"," do:",10,0,10,
                                                  Obecna_Knih::datum_ymd_dmy(@$kurz['do_data_skut']) );


        $this->misto_kurzu = new c_webform_cselect("misto_kurzu","Místo konání kurzu:",1,"view_s_misto_kurzu",@$kurz['id_s_misto_kurzu_fk'] );
        $this->misto_kurzu->Set_default($veta1['bude_upres']);
        
        $this->odberatel = new c_webform_cselect("odberatel","Odběratel:",1,"view_s_odberatel",@$kurz['id_s_odberatel_fk'] );
        $this->kontakt = new c_webform_cselect("kontakt","Kontakt pro další informace:",1,"c_kontakt",@$kurz['kontakt'] );



    } //construct
 
 
    
    
    
   public function Display($part='') {
        parent::Display('top');
   
   if (!($this->duplikat))      {
      echo '*formularove pole action*hidden:';
      echo '<input type="text" name="action" id="action" value="form" readonly>'."\n";
      echo "<br>"; 
      //echo  '<input type="hidden" name="action" id="action" value="form">'."\n";              
   }
   else {
     echo '*formularove pole action*hidden:';
     echo '<input type="text" name="action" id="action" value="form_dupl" readonly>'."\n";
     echo "<br>"; 
     //echo '<input type="hidden" name="action" id="action" value="form_dupl">'."\n";            
   }  
   
        $this->planovany_kurz_id->Display();
        //echo "<BR><BR>\n";
        
        if ($this->error_bool) {
           echo "<div ". webform_styles::$error. ">Ve formuláři jsou chyby!</div>";
        }
       
        if (!($this->duplikat)) {
             echo '<h3>Plánovaný kurz</h3>';
        }
        else {
            echo '<h3>Plánovaný kurz - duplikace</h3>'; 
            echo '<p ' .  webform_styles::$pokyn . '>Při duplikaci plánovaného kurzu je nutné změnit <b>alespoň jeden</b> z těchto údajů -
                 <b>Plánovaný kurz, Plánované datum od/do, Místo konání kurzu, Plánovaný počet účastníků min/max, Plánovaná cena, Odběratel</b>.
                  </p>';
        }
        
        
        $this->kurz_planovany->Display();
        echo "<BR><BR>\n";
        
        $this->od_data_plan->Display();
        //echo "<BR><BR>\n";
        $this->do_data_plan->Display();
        echo "<BR><BR>\n";
        
        // $this->od_data_skut->Display();
        // //echo "<BR><BR>\n";
        // $this->do_data_skut->Display();
        //echo "<BR><BR>\n";
        
        $this->misto_kurzu->Display();
        echo "<BR><BR>\n";
        
        $this->pocet_ucastniku_min_plan->Display();
        $this->pocet_ucastniku_max_plan->Display();
        echo "<BR><BR>\n";
       
        $this->cena_plan->Display();
        echo "<BR><BR>\n";
        $this->cena_plan_detail->Display();
        echo "<BR><BR>\n";
        
        
        $this->lektor->Display();
        echo "<BR><BR>\n";
        $this->odberatel->Display();
        echo "<BR><BR>\n";
        $this->kontakt->Display();
        echo "<BR><BR>\n";

        echo "<BR><BR>\n";
       
        parent::Display('buttons');
        parent::Display('bottom');
        
    }
    
    
    
    public function Drop() {
        if($this->planovany_kurz_id) {
            
           /* $sql_query = 'UPDATE planovane_kurzy 
                            SET deleted="1"
                            WHERE id_planovane_kurzy =  "' . $this->planovany_kurz_id->Get_value() . '"';*/
                            
            $sql_query = 'DELETE from planovane_kurzy 
                            WHERE id_planovane_kurzy =  "' . $this->planovany_kurz_id->Get_value() . '"';                 
            
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
            echo '<h3>Kurz byl odstraněn ze seznamu plánovaných kurzů</h3>';
            echo '<p>... vyčkejte, prosím </p><br><br>';
            echo '</body>';
            echo '</html>';
        }
    }
    

    
    public function Get_errors() {
        $this->error_bool=false;
         
        //test vnitřních hodnot
       
        if ( 
            $this->kurz_planovany->Get_errors() OR
            
            $this->pocet_ucastniku_min_plan->Get_errors() OR
            $this->pocet_ucastniku_max_plan->Get_errors() OR
            
            $this->od_data_plan->Get_errors() OR
            $this->do_data_plan->Get_errors() OR
            $this->od_data_skut->Get_errors() OR
            $this->do_data_skut->Get_errors() OR
            $this->cena_plan->Get_errors() OR
            $this->cena_plan_detail->Get_errors() OR                
            
            $this->lektor->Get_errors() OR
            $this->odberatel->Get_errors() OR
            $this->kontakt->Get_errors()

        )
        {
            $this->error_bool=true;
        }
        
        if ($this->duplikat) {
            if (
                (!$this->kurz_planovany->byla_zmena_hodnoty() ) and
                (!$this->pocet_ucastniku_min_plan->byla_zmena_hodnoty() ) and
                (!$this->pocet_ucastniku_max_plan->byla_zmena_hodnoty() ) and
                (!$this->od_data_plan->byla_zmena_hodnoty() ) and
                (!$this->do_data_plan->byla_zmena_hodnoty() ) and
                (!$this->cena_plan->byla_zmena_hodnoty() ) and                   
                (!$this->odberatel->byla_zmena_hodnoty() )  and
                (!$this->misto_kurzu->byla_zmena_hodnoty() )
               )    
            {
                 $this->error_bool=true;
                 $this->kurz_planovany->set_error("Nezměnil se ani kurz, ani plánované datum, ani místo konání,  ani počet účastníků min/max,  ani cena kurzu, ani odběratel!");
            }
        }
        
        
        
        // kontrola počtu osob
        if($this->pocet_ucastniku_max_plan->get_value() < $this->pocet_ucastniku_min_plan->get_value() ) {
            $this->error_bool=true;
            $this->pocet_ucastniku_max_plan->set_error("Minimální počet účastníků musí být menší než maximální počet účastníků!");
        }
        
        //kontrola datumu
        $er=false;
        if ($this->od_data_plan->get_value() )  {
          if (!(Obecna_Knih::kontrola_datum($this->od_data_plan->get_value())) ) {
           $er=true;
          }
        }
        if ($this->do_data_plan->get_value() )  {
          if (!(Obecna_Knih::kontrola_datum($this->do_data_plan->get_value())) ) {
             $er=true;
          }
        }
        if ($er) {
             $this->error_bool=true;
             $this->do_data_plan->set_error("Špatné datum!");
        }     
        
        $er=false;
        if ($this->od_data_skut->get_value() )  {
          if (!(Obecna_Knih::kontrola_datum($this->od_data_skut->get_value())) ) {
             $er=true;
          }
        }
        if ($this->do_data_skut->get_value() )  {
          if (!(Obecna_Knih::kontrola_datum($this->do_data_skut->get_value())) ) {
             $er=true;
          }
        }
        if ($er) {
           $this->error_bool=true;
           $this->do_data_skut->set_error("Špatné datum.");
        }
        
        
        if($this->error_bool) {
            return true;
        }
        else {
            return false;
        }
    }
    
    
    
    
    /**
     * $uloz_dupl=true  - pro duplikaci, ulozi INSERTem
     */
    public function Save($uloz_dupl=false) {
        //print_r($this);
        if(!$this->error_bool) {
            
            
            if ((!$this->planovany_kurz_id->Get_value()) or ($uloz_dupl) ) {     //insert
      
                $sql_query = 'INSERT INTO planovane_kurzy (
                                            id_kurz_FK,
                                            od_data_plan,
                                            do_data_plan,
                                            od_data_skut,
                                            do_data_skut,
                                            pocet_ucast_min_plan,
                                            pocet_ucast_max_plan,
                                            lektor,
                                            cena_plan,
                                            cena_plan_detail,
                                            id_s_misto_kurzu_FK,
                                            id_c_kontakt_FK,
                                            id_s_odberatel_FK                            
                                            )
                                    VALUES (
                    ' . $this->mdb2->quote($this->kurz_planovany->Get_value(),"integer"). ',' .
                    Obecna_Knih::priprav_datum_dodb($this->od_data_plan->Get_value() ) . ',' .
                    Obecna_Knih::priprav_datum_dodb($this->do_data_plan->Get_value() ) . ',' .
                   Obecna_Knih::priprav_datum_dodb($this->od_data_skut->Get_value() ) . ',' .
                   Obecna_Knih::priprav_datum_dodb($this->do_data_skut->Get_value() ) . ',' .
                    //' . $this->mdb2->quote("","datetime"). ',
                     $this->mdb2->quote($this->pocet_ucastniku_min_plan->Get_value(),"integer").',' .
                     $this->mdb2->quote($this->pocet_ucastniku_max_plan->Get_value(),"integer").',' .
                      $this->mdb2->quote($this->lektor->Get_value(), "text").',' .
                     
                     $this->mdb2->quote($this->cena_plan->Get_value(),"integer").', ' .
                     $this->mdb2->quote($this->cena_plan_detail->Get_value(),"text").', ' .
                        
                     $this->mdb2->quote($this->misto_kurzu->Get_value(),"integer"). ',
                    (SELECT id_c_kontakt FROM c_kontakt WHERE kod = '.$this->mdb2->quote($this->kontakt->Get_value(),"text"). '),
                    ' . $this->mdb2->quote($this->odberatel->Get_value(),"integer"). ' 
                    )';
      
               // print_r( $sql_query);
                
                $res=$this->mdb2->exec($sql_query);                                                                                     
                $this->planovany_kurz_id = new id("planovany_kurz_id",$this->mdb2->lastInsertID()); //'vyzvednout' nove id
                
            }    
            else {               //update
                
                $sql_query = 'UPDATE planovane_kurzy
                                SET ' .
                 'id_kurz_FK = ' . $this->mdb2->quote($this->kurz_planovany->Get_value(),"integer"). ',' .             
                 'od_data_plan = ' .  Obecna_Knih::priprav_datum_dodb ($this->od_data_plan->Get_value() )  . ','.
                                      //$this->mdb2->quote($dateDatOddataPlan,"date")-neumim
                 'do_data_plan = ' . Obecna_Knih::priprav_datum_dodb($this->do_data_plan->Get_value() ) . ',' .
                 'od_data_skut = ' . Obecna_Knih::priprav_datum_dodb($this->od_data_skut->Get_value() ) . ',' .
                 'do_data_skut = ' . Obecna_Knih::priprav_datum_dodb($this->do_data_skut->Get_value() ) . ',' .                                        
                   //'do_data_plan = ' . $this->mdb2->quote("","datetime"). ',
                   
                 'pocet_ucast_min_plan = ' . $this->mdb2->quote($this->pocet_ucastniku_min_plan->Get_value(),"integer").',' .
                 'pocet_ucast_max_plan = ' . $this->mdb2->quote($this->pocet_ucastniku_max_plan->Get_value(),"integer").',' .
                 'lektor  = ' . $this->mdb2->quote($this->lektor->Get_value(), "text").',' .
                 'cena_plan = ' . $this->mdb2->quote($this->cena_plan->Get_value(),"integer").',' .
                 'cena_plan_detail = ' . $this->mdb2->quote($this->cena_plan_detail->Get_value(),"text").',' .       
                 'id_s_misto_kurzu_FK = ' . $this->mdb2->quote($this->misto_kurzu->Get_value(),"integer"). ',' . 
                 'id_c_kontakt_FK = (SELECT id_c_kontakt FROM c_kontakt WHERE kod = '.$this->mdb2->quote($this->kontakt->Get_value(),"text"). '),' .
                 'id_s_odberatel_FK = ' . $this->mdb2->quote($this->odberatel->Get_value(),"integer").                      
                 ' WHERE id_planovane_kurzy ="'.$this->planovany_kurz_id->Get_value().'";';                        
                
                //echo "<BR>";
                //print_r($sql_query);
                
                $res=$this->mdb2->exec($sql_query);
                //echo "Updatuji";
            }
           
          
           //*** zde se zapisuje do pripadnych vazebnich tabulek ***
           
       
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
    } // function Save
    
} //class f_planovane_kurzy_kurz





?>