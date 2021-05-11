<?php
/**
 * Depository for web form objects
 * 
 * @name webform_textarea
 * @author
 * @version 1.0
 * @package webform_obj
 */
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_styles.inc";



/**
 *Třída c_webform_textarea slouží k zobrazení pole pro zadání delšího textu ve www formuláři.
 *Po odeslání validuje minimální a maximální délku text
 *Je možné přednastavit text, který bude textarea obsahovat
 */
class c_webform_textarea {
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
    /**
     *Constructor
     *@param string $item_name Název, pod kterým bude položka vložena do formuláře
     *@param string $item_description Text, který bude zobrazen u položky ve formuláři uživateli
     *@param int $item_size_cols Šířka zobrazené textarey
     *@param int $item_size_rows Počet řádek zobrazené textarey
     *@param string $ciselnik_db_name Název tabluky s položkami
     *@param string $default_value Kód výchozí hodnoty čísleníku, pokud je uveden, bude předvybrána příslušná hodnota,
     * pokud je uveden neexistující, zobrazí se jako výchozí hodnota první dle seznamu, pokud není uvedena, nebo pokud je
     * je jako kod nastaven prázdný řetězec '' tak se zobrazí výzva k výběru
     *@param string $old_value Kód pro přednastavení hodnoty při editaci formuláře, je možné nastavit i kód smazaných položek
     *(ve sloupci deleted =1) tak aby mohl být znovu uložen formulář s původními třeba již smazanými položkami číselníku
     * @access  public
    */
    public function __construct($item_name,$item_description,$item_size_cols,$item_size_rows,$min_chars,$max_chars,$old_value=false) {
        $this->item_name=$item_name;
        $this->item_description=$item_description;
        $this->item_size_cols=$item_size_cols;
        $this->item_size_rows=$item_size_rows;
        $this->min_chars=$min_chars;
        $this->max_chars=$max_chars;
        $this->old_value=$old_value;
        $this->posted_value=false;
        
        //Načtení hodnoty z pole _POST
        //print_r($_POST);
        if( isset($_POST[$this->item_name])) {
            if(strlen(trim($_POST[$this->item_name]))<$this->min_chars){
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
                $this->posted_value=html_entity_decode(trim($_POST[$this->item_name]),ENT_QUOTES);
            }
            elseif (strlen(trim($_POST[$this->item_name]))>$this->max_chars){
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
                $this->posted_value=substr(html_entity_decode(trim($_POST[$this->item_name]),ENT_QUOTES),0,$this->max_chars);
            }
            else {
                $this->error_bool=false;
                $this->error_text='';
                $this->posted_value=html_entity_decode(trim($_POST[$this->item_name]),ENT_QUOTES);
            }
        }
        
        //echo "<br><br>construct Itemname textarea**" . $this->item_name ;    // *SEL*
        //echo "<br>Oldvalue textarea**" . $this->old_value ;
        //echo "<br>Defaultvalue textarea**" . $this->default_value ;
        //echo "<br>Postedvalue textarea**" . $this->posted_value ;
        
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
        }
        else {
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
        if($parts=='ALL' or $parts=='DESC') {
            echo $this->item_description.'<BR>';
        }
        if($parts=='ALL' or $parts=='INPUT') {
            if(!$this->posted_value) {
                if($this->old_value) {
                    $this->posted_value=$this->old_value;
                }
                else if ($this->default_value) {
                    $this->posted_value=$this->default_value;
                }
            }
            echo '<TEXTAREA name="'.$this->item_name.'"
                            cols="'.$this->item_size_cols.'"
                            rows="'.$this->item_size_rows.'" '.
                            webform_styles::$textarea.'>';
            echo htmlspecialchars($this->posted_value,ENT_QUOTES);
            echo '</TEXTAREA>'."\n";
            
            //echo '<br><span '. webform_styles::$drobna_poznamka .'>' . $this->item_name . '</span>';     // *SEL* ladici
            if($this->error_bool) {
                echo '<SPAN '.webform_styles::$error.'><BR>Chyba: '.$this->error_text.'</SPAN>'."\n";
            }
            echo '<BR>'."\n";
        }
    }
    
    
}


?>
