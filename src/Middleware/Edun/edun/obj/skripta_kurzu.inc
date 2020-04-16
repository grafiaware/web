<?php
/**
 * Object depository for katalog_kurzu.php
 * 
 * @name skripta_kurzu.inc
 * 
 * @version 1.0
 * @package webform_obj
 */

/*require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform.inc";
*/

require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/VynucenyDownload.php";

define('SOUBOR_SKRIPT_PRIPONA', ".html");
define('SOUBOR_SKRIPT_PATH', Middleware\Edun\AppContext::getAppPublicDirectory()."/skripta/");

define ('SOUBOR_STYLU_SKRIPTA', Middleware\Edun\AppContext::getAppPublicDirectory()."/grafia/css/styles_skripta.css");


//          	P.western { font-family: \"Times New Roman\", serif; font-size: 12pt; so-language: cs-CZ }
//		  BODY,DIV,TABLE,THEAD,TBODY,TFOOT,TR,TH,TD,P { font-family:\"Arial\"; font-size:x-small }

define ('ZACATEK_HTML',
"<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
  <HTML>
  <HEAD>
	<META HTTP-EQUIV=\"CONTENT-TYPE\" CONTENT=\"text/html; charset=utf-8\">
  	<TITLE></TITLE>
        <STYLE>
    <!-- 
        
");
	  

/*
define ('STYLE_CSS',
*/ 

define ('STYLE_CSS_KONEC',
" 
 -->
 </STYLE>
 </HEAD>
 <BODY>
 ");


define ('KONEC_HTML',
" </BODY></HTML>
");


/***********************************************************************************/



class o_skripta_kurzu {
    //private $error_bool=false;
    
    private $mdb2;
    
    private $kurz_id;
    
    private $kurz_kod;
    private $kurz_nazev;
    private $skripta_obsah;    
    private $soubor_skript;
    private $soubor_skript_stylu;

    private $zacatek_html =  ZACATEK_HTML;
    private $konec_html =  KONEC_HTML;
    private $style_css ;          // STYLE_CSS;
    private $style_css_konec =  STYLE_CSS_KONEC;
    
    /**
     *Constructor
     *
     *@param $kurz_id
     *
     * @access  public
     */
    
    public function __construct($kurz_id=false) {
       
    if($kurz_id) {
           
        $this->mdb2 = Middleware\Edun\AppContext::getDb();
           // echo "<br>Tady udelam objekt skript!!!!";
        $this->kurz_id=$kurz_id;
           // echo "<br>-this-kurz_id*" . $this->kurz_id ;
            
        
        
        
       $sql_query1 = 'SELECT * from  kurz 
                    left join popis_kurzu on kurz.id_popis_kurzu_FK = popis_kurzu.id_popis_kurzu 
                    where kurz.id_kurz =' . $this->kurz_id ;
       $res=$this->mdb2->query($sql_query1);
       if ($res) {
           $kk = $res->fetch(\PDO::FETCH_ASSOC);
           //$this->kurz_kod=$kk['kod_kurzu'];
           $this->kurz_nazev=$kk['kurz_Nazev'];
       } 
        
       $this->soubor_skript = SOUBOR_SKRIPT_PATH . "skripta_". $this->kurz_kod .  "_" . str_replace(" ","_",mb_substr($this->kurz_nazev,0,50)) . SOUBOR_SKRIPT_PRIPONA;
       $this->soubor_skript_stylu = SOUBOR_STYLU_SKRIPTA;
       //echo "<br>***soubor skript stylu " . $this->soubor_skript_stylu;
        
        //-----------------------  
        $obsah="";    
        $sql_query = 'SELECT * 
                      from  vzb_kurz_cast_kurzu 
                      left join cast_kurzu on (vzb_kurz_cast_kurzu.id_cast_kurzu_FK = cast_kurzu.id_cast_kurzu) 
                      left join kurz on (vzb_kurz_cast_kurzu.id_kurz_FK=kurz.id_kurz)
                      left join popis_kurzu on (kurz.id_popis_kurzu_FK=popis_kurzu.id_popis_kurzu) 
                      where id_kurz_FK=' .  $this->kurz_id . ' order by razeni';
        //echo $sql_query;
        $res_casti=$this->mdb2->query($sql_query);
        if ($res_casti) {
             
            while ($cast = $res_casti->fetch(\PDO::FETCH_ASSOC)) {
                  //echo( "<br>*-". $cast['id_cast_kurzu_fk'] ."*". $cast['cast_nazev_skripta']);  
                
                 $obsah .=  '<p class="nadpis-casti">' . $cast['cast_nazev'] . '</p>';                             
                  //echo ("<br>Cast" .$cast['cast_nazev']);
                 
                 
                 $sql_query = 'SELECT * from  vzb_cast_modul_kurzu 
                      left join modul_kurzu on (vzb_cast_modul_kurzu.id_modul_kurzu_FK=modul_kurzu.id_modul_kurzu)
                      where id_cast_kurzu_FK=' .  $cast['id_cast_kurzu_FK'] . ' order by razeni';
                 $res_moduly=$this->mdb2->query($sql_query);
                 if ($res_moduly) {
                     
                     while ($modul = $res_moduly->fetch(\PDO::FETCH_ASSOC)) {
                        //echo( "<br>&nbsp;&nbsp;&nbsp;&nbsp;-". $modul['id_modul_kurzu_fk'] ."*");  
                        
                        $obsah .= '<p class="nadpis-modulu">' .$modul['modul_nazev']. '</p>';  
                        $obsah .= $modul['modul_obsah_skripta']; 
                                    
                     }  //while modul
                     
                 }//res_moduly
                
                
            } //while cast   
            
        
         }//res_casti
     
         //echo "<br>kurz_kod" . $this->kurz_kod . " " .$this->kurz_nazev;
         
         $this->skripta_obsah =  $obsah;    
         //print_r($this->skripta_obsah); echo ("<br>*******************");
       //---------------------------------------------------------------------------  
         
         
         
       //$aktualni=getcwd();
       //echo "<br>******" . $aktualni;
       
       if (is_file($this->soubor_skript_stylu)) {
            $fp=fopen($this->soubor_skript_stylu,"r") ;
            if ($fp) {
                 $this->style_css = fread ($fp, filesize ($this->soubor_skript_stylu));
                 fclose ($fp); 
             }  else {echo "<br>Problémy se souborem stylů skript. (".$this->soubor_skript_stylu .")";}
       } 
       else { echo "<br>Soubor stylů skript neexistuje. (".$this->soubor_skript_stylu .")";}
       
       //$this->style_css= STYLE_CSS;
       //echo "<br>******" . $this->style_css;
       
         
        
        
    } //id
    
}//construct




 private function zapis_do_souboru() {
      if ($this->skripta_obsah) {
          
        $fp=fopen($this->soubor_skript,"w") ;
        if ($fp) {
             fwrite($fp,$this->zacatek_html . $this->style_css . $this->style_css_konec . $this->skripta_obsah .  $this->konec_html);
             fclose($fp);   
        }
      }
     
 }//zapis_do_souboru()
 

 
 public function display() {
     if ($this->skripta_obsah) {
            echo "<H2>" . $this->kurz_kod . " " .$this->kurz_nazev. "</H2>";
            echo $this->zacatek_html . $this->style_css .  $this->style_css_konec . $this->skripta_obsah  . $this->konec_html;
            //echo  ' <STYLE> <!-- ' . $this->style_css .  $this->style_css_konec . $this->skripta_obsah  . $this->konec_html;
           // echo   $this->skripta_obsah  ;
     }
     else echo  "<br>Skripta kurzu  <b>" . $this->kurz_kod . " " .$this->kurz_nazev . " </b> nejsou k dispozici.";
     
 }//display()
 
 
  public function download() {
     if ($this->skripta_obsah) {
            //echo "<H2>download</H2> . $this->soubor_skript;
           $this->zapis_do_souboru();
           
           VynucenyDownload::download($this->soubor_skript);
     }
     else echo "<br>Skripta kurzu  <b>" . $this->kurz_kod . " " .$this->kurz_nazev . " </b> nejsou k dispozici.";
     
 }//download
 
    
    
}// class o_skripta_kurzu 



?>
