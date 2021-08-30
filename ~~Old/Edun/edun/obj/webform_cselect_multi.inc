<?php
/**
 * Depository for web form objects
 * 
 * @name webform_cselect_multi
 * @author
 * @version 1.0
 * @package webform_obj
 */
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_styles.inc";



/**
 *Třída c_webform_cselect_multi slouží k zobrazení pole select ve www formuláři.
 *Umožňuje výběr více hodnot a to metodou přidávání do formuláře, kde je k selectu
 *přidáno tlačítko "Přidat" a formulář je odeslán ke zpracování a znovu zobrazen s předtím
 *vybranou položkou.
 *U položek je tlačítko ke smazání z výběru
 *Obsah pole získává z databáze z tabulky typu ciselnik
 *Po odeslání validuje vrácenou hodnotu
 *Je možné přednastavit výchozí položku výběru
 */
class c_webform_cselect_multi  extends c_webform_cselect{
// SV: c_webform_cselect_multi je potomek c_webform_cselect a oba měli deklarované proměnné jako private - přitom některé proměnné se nastavují v rodiči  některé v potomku udělal jsem z nich protected  a nechal je jen v rodiči
    
    public function __construct($item_name,$item_description,$item_size,$ciselnik_db_name,$old_value=false) {
        $this->old_value=$old_value;
        $this->item_name=$item_name;

        parent::__construct('sub_'.$this->item_name,$item_description,$item_size,$ciselnik_db_name,array());
       
        if( isset($_POST[$this->item_name])) {
            $this->posted_value=$_POST[$this->item_name];
        }

        //TODO: Svoboda - provizorně upravená třída webform - přidáno získání cesty ke složce s js skripty z AppContext
        // následuje zakomentovaný původní kód - získání path_to_app takto nefunguje - nepoužívám změnu
        // pracovního adresáře a tedy vždy vyjde $this->path_to_app jako prázdný string
//        $script_path = explode('/',$_SERVER['SCRIPT_FILENAME']);
//        array_pop($script_path);
//        $script_path=implode('/',$script_path);
//        $cwd = str_replace('\\','/',getcwd());
//        $this->path_to_app = explode($script_path,$cwd);
//        $this->path_to_app = $this->path_to_app[1];
        // nově - absolutní adresa:
        $this->path_to_app = Middleware\Edun\AppContext::getAppPublicDirectory();        
         
        //echo "<br><br>construct Itemname multi **" . $this->item_name ;     // *SEL*
        //echo "<br>Oldvalue multi **"; print_r($this->old_value );
        //echo "<br>Defaultvalue multi **"; print_r($this->default_value );
        //echo "<br>Postedvalue multi **";  print_r($this->posted_value) ;
        
    }
    
    public function set_default($default_value) {
        $this->default_value=$default_value;
    }
    
    public function get_value() {
        if ($this->posted_value) {
            return $this->posted_value;
        }
        else {
            return array();
        }
    }
    
     public function get_old_value() {
        if ($this->old_value) {
            return $this->old_value;
        }
        else {
            return array();
        }
    }
    
    
    public function get_errors() {
        if($this->error_bool) {
            return true;
        }
        else {
            return false;
        }
    }
    
    public function set_error($error_text) {
        $this->error_bool=true;
        $this->error_text .=" ".$error_text;
    }
    
    public function Display($parts='ALL') {        
        if($parts=='ALL' or $parts=='DESC') {
            parent::Display('DESC');
        }
        if($parts=='ALL' or $parts=='INPUT') {
            parent::Display('INPUT');
            
            if(!$this->posted_value) {
                if($this->old_value) {
                    $this->posted_value=$this->old_value;
                }
                else if($this->default_value) {
                    $this->posted_value=$this->default_value;
                }
                else {
                    $this->posted_value = array();
                }
            }
       
            if(self::$first_load) {
                echo '<script type ="text/javascript" src=".'.$this->path_to_app.'/obj/cselect_multi.js"></script>'."\n";
                self::$first_load=false;
            }
            
             
            //echo '&nbsp;&nbsp;&nbsp;**** '. $this->item_name .  ' ****';       // *SEL* ****
            echo '<input type="button" name="button_'.$this->item_name.'" id="button_'.$this->item_name.'"
            value="Přidat" onClick="cselect_multi_add_to_div(\''.$this->item_name.'\');">'."\n";
            
            //echo '<br><span '. webform_styles::$drobna_poznamka .'>' . 'následuje div_'  . $this->item_name . '</span>';    // *SEL* ladici
                       
            echo '<br><div id="div_'.$this->item_name. '"'. webform_styles::$multi . '>'."\n";
            echo '</div>'."\n";        
                        
            echo '<script type ="text/javascript">'."\n";
            if($this->posted_value) {
                echo 'default_values["'.$this->item_name.'"]=new Array("'.implode("\",\"",$this->posted_value).'");'."\n";
            }
            echo 'cselect_multi_instances[cselect_multi_instances.length]="'.$this->item_name.'";'."\n";
            echo '</script>'."\n";
           
        }
    }//function Display
}//class c_webform_cselect_multi


?>
