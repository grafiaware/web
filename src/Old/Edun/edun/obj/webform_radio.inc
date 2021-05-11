<?php
/**
 * Depository for web form objects
 * 
 * @name webform_radio
 * @author 
 * @version 1.0
 * @package webform_obj
 */
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_styles.inc";



/**
 *Třída c_webform_radio slouží k zobrazení pole radio buttonu ve www formuláři.
 *
 *
 *Je možné přednastavit výchozí položku výběru
 */
class c_webform_radio {
    private $item_name;
    private $item_description;
    private $item_part_description;
    private $item_size;
    
    private $old_value;
    private $default_value;
    private $error_bool;
    private $error_text;
    private $posted_value=false;
    private $mdb2;
    /**
     *Constructor
     *@param string $item_name Název, pod kterým bude prvek(položka) vložen do formuláře
     *@param string $item_description Text, který bude zobrazen u položky ve formuláři uživateli
     *@param int $item_size Počet puntíků, které se zobrazí v položce
     *@param string $default_value výchozí hodnota , pokud je uveden, bude předvybrána příslušná hodnota
     *@param string $old_value  pro přednastavení hodnoty při editaci formuláře
     *
     * @access  public
    */
    public function __construct($item_name,$item_description,$item_part_description,$item_size,$old_value=false) {
        $this->item_name=$item_name;
        $this->item_description=$item_description;
        $this->item_part_description=$item_part_description;
        $this->item_size=$item_size;
     
        $this->old_value=$old_value; 
        $this->posted_value=$this->old_value;   
        
        $this->mdb2 = Middleware\Edun\AppContext::getDb();
       
        //Načtení hodnoty z pole _POST
        
        if( isset($_POST[$this->item_name])) {
           
                $this->error_bool=false;
                $this->error_text='';
                $this->posted_value=html_entity_decode($_POST[$this->item_name],ENT_QUOTES);
        }
                        
        //echo "<br><br>construct Itemname radio**" . $this->item_name ;    // *SEL*
        //echo "<br>Oldvalue radio**"; print_r($this->old_value );
        //echo "<br>Defaultvalue radio**"; print_r($this->default_value );
        //echo "<br>Postedvalue radio**";  print_r($this->posted_value) ;
        
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
            echo htmlspecialchars($this->item_description,ENT_QUOTES).' <br>';
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
            
            for ($i = 1; $i <= $this->item_size; $i++) {
              
              echo '<input  id="' . $this->item_name . $i . '" ' .
                            'type="radio" ' . 
                            'name="' . $this->item_name . '" ' .
                            'onClick= "zobr_radio_click(\'' . $this->item_name .$i . '\');" ' .
                            'value="'. $i .'" ';
              if ($i== htmlspecialchars($this->posted_value,ENT_QUOTES)) {
                echo ' checked';
              }
              echo '>';
              echo $this->item_part_description[$i-1] . " &nbsp;&nbsp;&nbsp;&nbsp;";
             
            }
                           
         }
            //echo '<br><span '. webform_styles::$drobna_poznamka .'>' . $this->item_name . '</span>';     // *SEL* ladici
            if($this->error_bool) {
                echo '<SPAN '.webform_styles::$error.'><BR>Chyba: '.$this->error_text.'</SPAN>'."\n";
            }
   
    }//function Display
    
    
} //class c_webform_radio


?>




    
    