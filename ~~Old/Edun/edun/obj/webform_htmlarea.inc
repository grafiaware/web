<?php
/**
 * Depository for web form objects
 * 
 * @name webform_htmlarea
 * @author
 * @version 1.0
 * @package webform_obj
 */
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_styles.inc";

/**
 *Třída c_webform_htmlarea slouží k zobrazení pole pro zadání delšího textu ve www formuláři.
 *Po odeslání validuje minimální a maximální délku text
 *Je možné přednastavit text, který bude textarea obsahovat
 */
class c_webform_htmlarea {
    private $item_name;
    private $item_description;
    private $item_size_cols; //počet znaků na šířku
    private $item_size_rows; //počet řádek
    private $min_chars;
    private $max_chars;
    private $default_value;
    private $old_value;
    private $error_bool;
    private $error_text;
    private $posted_value;
    private $typ_stylu;
    private static $first_load=true;
    private static $first_load_pro_skripta=true;
    
    
    /**
     *Constructor
     *@param string $item_name  Název, pod kterým bude položka vložena do formuláře
     *@param string $item_description  Text, který bude zobrazen u položky ve formuláři uživateli
     *@param int $item_size_cols  Šířka zobrazené textarey
     *@param int $item_size_rows  Počet řádek zobrazené textarey
     *@param string $ciselnik_db_name  Název tabluky s položkami
     *@param string $default_value  Kód výchozí hodnoty čísleníku, pokud je uveden, bude předvybrána příslušná hodnota,
     * pokud je uveden neexistující, zobrazí se jako výchozí hodnota první dle seznamu, pokud není uvedena, nebo pokud je
     * je jako kod nastaven prázdný řetězec '' tak se zobrazí výzva k výběru
     *@param string $old_value Kód pro přednastavení hodnoty při editaci formuláře, je možné nastavit i kód smazaných položek
     *(ve sloupci deleted =1) tak, aby mohl být znovu uložen formulář s původními třeba již smazanými položkami číselníku
     *@param string $typ_stylu  podle hodnoty 'defaultni'|'pro_skripta' inicializujeme selectitko tinyMCE --Styly--
     * 
     * @access  public
    */
    public function __construct($item_name,$item_description,$item_size_cols,$item_size_rows,$min_chars,$max_chars,$old_value=false,$typ_stylu='defaultni') {
        
        $this->item_name=$item_name;
        $this->item_description=$item_description;
        $this->item_size_cols=$item_size_cols;
        $this->item_size_rows=$item_size_rows;
        $this->min_chars=$min_chars;
        $this->max_chars=$max_chars;
        $this->old_value=$old_value;
        $this->typ_stylu=$typ_stylu;
        $this->posted_value=false;
        
        if( isset($_POST[$this->item_name])) {
            if(strlen(trim(html_entity_decode ($_POST[$this->item_name],ENT_QUOTES,"UTF-8")))<$this->min_chars){
                $this->error_bool=true;
                $this->error_text='Položka musí mít minimálně '.$this->min_chars;
                //echo "Zkoumám číslo:" .substr($this->min_chars,strlen($this->min_chars)-1);
                switch(substr($this->min_chars,strlen($this->min_chars)-1)){
                    case 1:
                        $this->error_text .= ' znak';
                        break;
                    case 2:
                    case 3:
                    case 4:
                        $this->error_text .= ' znaky';
                        break;
                    default:
                        $this->error_text .= ' znaků';
                }
                $this->error_text .= "!\n";
                $this->posted_value=html_entity_decode(trim(html_entity_decode ($_POST[$this->item_name],ENT_QUOTES,"UTF-8")),ENT_QUOTES);
            } elseif (strlen(trim(html_entity_decode ($_POST[$this->item_name],ENT_QUOTES,"UTF-8")))>$this->max_chars){
                $this->error_bool=true;
                $this->error_text='Položka může mít maximálně '.$this->max_chars;
                //echo "Zkoumám číslo:" .substr($this->max_chars,strlen($this->max_chars)-1);
                switch(substr($this->max_chars,strlen($this->max_chars)-1)){
                    case 1:
                        $this->error_text .= 'znak';
                        break;
                    case 2:
                    case 3:
                    case 4:
                        $this->error_text .= 'znaky';
                        break;
                    default:
                        $this->error_text .= 'znaků';
                }
                $this->error_text .= "!\n";
                $this->posted_value=substr(html_entity_decode(trim(html_entity_decode ($_POST[$this->item_name],ENT_QUOTES,"UTF-8")),ENT_QUOTES),0,$this->max_chars);
            } else {
                $this->error_bool=false;
                $this->error_text='';
                $this->posted_value=html_entity_decode(trim(html_entity_decode ($_POST[$this->item_name],ENT_QUOTES,"UTF-8")),ENT_QUOTES);
            }
        }
    }
    
    public function set_default($default_value) {
        $this->default_value=$default_value;
    }
    
    public function get_value() {
        return $this->posted_value;
    }

    public function get_errors() {
        if($this->error_bool) {
            return true;
        } else {
            return false;
        }
    }
    
    public function set_error($error_text) {
        $this->error_bool=true;
        $this->error_text .=" ".$error_text;
    }
    
    /**
    *Display
    *metoda pro zobrazení prvku ve formuláři
    * @param [ALL|DESC|INPUT] $parts - nepovinný parametr - pokud není zadán zobrazí se popis položky a následně vstupní pole
    *  při nastavení $parts = DESC se zobrazí jen popis položky
    *  při nastavení $parts = INPUT se zobrazí jen vstupní pole a pod ním případně chybové hlášení
    * @access  public
    */

    public function Display($parts='ALL') {
        //TODO: Svoboda - provizorně - cesta k souborům htmlarea
        $tinyMceJsFile = Middleware\Edun\AppContext::getPublicDirectory().'tinymce/tinymce.min.js';
        $htmlareaDefaultConfigFile = Middleware\Edun\AppContext::getAppPublicDirectory().'grafia/js/HTMLarea_default.js';
        $htmlareaSkriptaConfigFile = Middleware\Edun\AppContext::getAppPublicDirectory().'grafia/js/HTMLarea_skripta.js';

        if($parts=='ALL' or $parts=='DESC') {
            echo $this->item_description .  '<BR>';
        }
        if($parts=='ALL' or $parts=='INPUT') {
            if ((self::$first_load_pro_skripta)and (self::$first_load)) {          
                echo '<script language="javascript" type="text/javascript" src="'.$tinyMceJsFile.'"></script>'."\n";
            }
            if ($this->typ_stylu=='defaultni') {                
                if (self::$first_load) {
                    self::$first_load=false;                                                          
                    echo '<script language="javascript" type="text/javascript" src="'.$htmlareaDefaultConfigFile.'"></script>'."\n"; 
                }    
                else { 
                    //echo 'neni pro skripta, je defaultni - JDE TUDY- kazda dalsi ';
                }
            }
            if ($this->typ_stylu=='pro_skripta') {
                if (self::$first_load_pro_skripta) {
                    //echo '<script language="javascript" type="text/javascript">' ; //
                    //echo 'alert(\' je to first_load!\')' ;   //
                    //echo '</script>';   //                               
                    self::$first_load_pro_skripta=false;                     
                    //echo 'Toto je PRO SKRIPTA'; 
                    echo '<script language="javascript" type="text/javascript" src="'.$htmlareaSkriptaConfigFile.'"></script>'."\n"; //neni styl,ale umisteni souboru
                } else {    
                   //echo 'pro skripta - JDE TUDY- kazda dalsi';                                    
                }
            }

            //----------------------------------------
            if(!$this->posted_value) {
                if($this->old_value) {
                    $this->posted_value=$this->old_value;
                } else if ($this->default_value) {
                    $this->posted_value=$this->default_value;
                }
            }
            if ($this->typ_stylu=='pro_skripta') { 
                echo '<DIV '. webform_styles::$skripta_background . ' >';
            }
            echo '<TEXTAREA ';
            if ($this->typ_stylu=='defaultni') {       
                echo ' id="' . $this->item_name . '" ' . ' class="clas_edit_bezne" ';      //"id_defaultni" ';    //_' . $this->item_name . '" ' ;
            }
            if ($this->typ_stylu=='pro_skripta') {     
                echo  ' id="' . $this->item_name . '" ' . ' class="clas_edit_skripta" ' ;
            }
            echo ' name="'.$this->item_name . '"' ;
            echo     webform_styles::$textarea . '>';
            echo htmlspecialchars($this->posted_value,ENT_QUOTES); 
            echo '</TEXTAREA>'."\n";
            if ($this->typ_stylu=='pro_skripta') { 
                echo '</DIV>';
            }
            if($this->error_bool) {
                echo '<SPAN '.webform_styles::$error.'><BR>Chyba: '.$this->error_text.'</SPAN>'."\n";
            }
            echo '<BR>'."\n";
        }
    }
}



?>
