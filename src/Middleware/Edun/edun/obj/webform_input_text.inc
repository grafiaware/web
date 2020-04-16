<?php
/**
 * Depository for web form objects
 * 
 * @name webform_input_text
 * @author
 * @version 1.0
 * @package webform_obj
 */
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_styles.inc";




/**
 *Třída c_webform_input_text slouží k zobrazení pole input ve www formuláři.
 *Třída hlídá minimální a maximální počet zadaných znaků
 */
    
class c_webform_input_text {
    private $item_name;
    private $item_description;
    private $item_size;
    private $min_chars;
    private $max_chars;
    private $old_value;
    private $default_value=false;
    private $error_bool;
    private $error_text;
    private $posted_value=false;
    /**
     *Constructor
     *@param string $item_name Název pod kterým bude položka vložena do formuláře
     *@param string $item_description Text který bude zobrazen u položky ve formuláři uživateli
     *@param int $item_size Počet řádků které se zobrazí v položce
     *
     *@param string $ciselnik_db_name Název tabluky s položkami   ???
     *@param string $default_value Kód výchozí hodnoty čísleníku, pokud je uveden, bude předvybrána příslušná hodnota,  ???
     * pokud je uveden neexistující, zobrazí se jako výchozí hodnota první dle seznamu, pokud není uvedena, nebo pokud je   ???
     * je jako kod nastaven prázdný řetězec '' tak se zobrazí výzva k výběru   ???
     * 
     *@param string $old_value Kód pro přednastavení hodnoty při editaci formuláře, je možné nastavit i kód smazaných položek (ve sloupci deleted =1)
     * ... aby mohl být znovu uložen formulář s původními třeba již smazanými položkami číselníku 
     * @access  public
    */
    public function __construct($item_name,$item_description,$item_size,$min_chars,$max_chars,$old_value=false) {
        $this->item_name=$item_name;
        $this->item_description=$item_description;
        $this->item_size=$item_size;
        $this->min_chars=$min_chars;
        $this->max_chars=$max_chars;
        $this->old_value=$old_value;
        //Načtení hodnoty z pole _POST
        //print_r($_POST);    // / *SEL* /
         
        if( isset($_POST[$this->item_name])) {
            //echo "<br>00<br>*"; 
            if(strlen(trim($_POST[$this->item_name]))<$this->min_chars){
                // echo "<br>A<br>";
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
                // echo "<br>B<br>";
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
                //echo "<br>C<br>";
                $this->error_bool=false;
                $this->error_text='';
                $this->posted_value=html_entity_decode($_POST[$this->item_name],ENT_QUOTES);
            }
        }
        
        //echo "<br><br>construct Itemname inputtext**" . $this->item_name ;   // *SEL*
        //echo "<br>Oldvalue inputtext**" . $this->old_value . '*' ;
        //echo "<br>Defaultvalue inputtext**" . $this->default_value . '*';
        //echo "<br>Postedvalue inputtext**" . $this->posted_value . '*';
        
    }
    
    public function set_default($default_value) {
        $this->default_value=$default_value;
    }
    
    public function get_value() {
        return $this->posted_value;
    }
    
    
    
    
    
    public function get_errors() {
       // echo "<br><br>get errors Itemname inputtextGE**" . $this->item_name ;   // *SEL*
       // echo "<br>Oldvalue inputtextGE**" . $this->old_value ;
       // echo "<br>Defaultvalue inputtextGE**" . $this->default_value ;
       // echo "<br>Postedvalue inputtextGE**" . $this->posted_value ;
       
        
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
    
    
    public function byla_zmena_hodnoty() {
        
        //echo "<br><br>FFFfcebyla zmena hodnoty input text";
        //echo "<br>old " . $this->old_value;
        //echo "<br>posted " . $this->posted_value;
        
        if ($this->posted_value) {
             if ($this->old_value == $this->posted_value) { return false; }
             else {  return true; }
        }
        else {return false;    }
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
            echo htmlspecialchars($this->item_description,ENT_QUOTES).' ';
        }
        if($parts=='ALL' or $parts=='INPUT') {
            if (!$this->posted_value)  {               
                if (($this->old_value) or ($this->old_value==='0')) {      //doplneno ===0,... aby se zobrazovala
                    $this->posted_value=$this->old_value;
                }
                else if ($this->default_value) {
                    $this->posted_value=$this->default_value;
                }
            }
            echo '<input    type="Text"
                            name="'.$this->item_name.'"
                            value="'.htmlspecialchars($this->posted_value,ENT_QUOTES).'"
                            size="'.$this->item_size.'"
                            MAXLENGTH="'.$this->max_chars.'" '.
                            webform_styles::$input_text.'>'."\n";
            
            //echo '<br><span '. webform_styles::$drobna_poznamka .'>' . $this->item_name . '</span>';     // *SEL* ladici    
            if($this->error_bool) {
                echo '<SPAN '.webform_styles::$error.'><BR>Chyba: '.$this->error_text.'</SPAN>'."\n";
            }
        }
    }
    
    
} //c_webform_input_text


?>
