<?php
/**
 * Depository for web form objects
 * 
 * @name webform_cselect
 * @author
 * @version 1.0
 * @package webform_obj
 */
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_styles.inc";



/**
 *Třída c_webform_cselect slouží k zobrazení pole select ve www formuláři.
 *Obsah pole získává z databáze z tabulky typu ciselnik
 *Po odeslání validuje vrácenou hodnotu
 *Je možné přednastavit výchozí položku výběru
 */
class c_webform_cselect {
    protected $item_name;
    private $item_description;
    private $item_size;
    private $ciselnik_db_name;
    protected $old_value;
    protected $default_value;
    protected $error_bool;
    protected $error_text;
    protected $posted_value=false;
    private $mdb2;
    
    protected static $first_load=true;
    protected $path_to_app;    
    /**
     *Constructor
     *@param string $item_name Název, pod kterým bude položka vložena do formuláře
     *@param string $item_description Text, který bude zobrazen u položky ve formuláři uživateli
     *@param int $item_size Počet řádků, které se zobrazí v položce
     *@param string $ciselnik_db_name Název tabluky s položkami
     *@param string $default_value Kód výchozí hodnoty číselníku, pokud je uveden, bude předvybrána příslušná hodnota,
     * pokud je uveden neexistující, zobrazí se jako výchozí hodnota první dle seznamu,
     * pokud není uvedena, nebo pokud je jako kod nastaven prázdný řetězec '', tak se zobrazí výzva k výběru
     *@param string $old_value Kód pro přednastavení hodnoty při editaci formuláře,
     *je možné nastavit i kód smazaných položek (ve sloupci deleted =1) tak,aby mohl být znovu uložen formulář s původními třeba již smazanými položkami číselníku
     *
     * @access  public
    */
    public function __construct($item_name,$item_description,$item_size,$ciselnik_db_name,$old_value=false) {
        $this->item_name=$item_name;
        $this->item_description=$item_description;
        $this->item_size=$item_size;
        $this->ciselnik_db_name=$ciselnik_db_name;
        $this->old_value=$old_value;
        $this->posted_value=$this->old_value;   
        
        $this->mdb2 = Middleware\Edun\AppContext::getDb();
        //Načtení hodnoty z pole _POST
        if( isset($_POST[$this->item_name])) {
            if($_POST[$this->item_name]=='----'){
                $this->error_bool=true;
                $this->error_text='Výběr nebyl proveden, vyberte znovu!';
                $this->posted_value='';
            }
            else {
                $sql_query = 'SELECT kod,text FROM '.$this->ciselnik_db_name.' WHERE kod="'.$_POST[$this->item_name].'"
                                AND (deleted=0 OR kod="'.$this->old_value.'") ORDER BY razeni ASC';
                $res=$this->mdb2->query($sql_query);
                if($res->rowCount()) {
                    $this->error_bool=false;
                    $this->error_text='';
                    $this->posted_value=$_POST[$this->item_name];
                }
                else {
                    $this->error_bool=true;
                    $this->error_text='Položka číselníku nenalezena!';
                    $this->posted_value='';
                }
            }
        }
        
        //echo "<br><br>construct Itemname cselect**" . $this->item_name ;    // *SEL*
        //echo "<br>Oldvalue cselect**"; print_r($this->old_value );
        //echo "<br>Defaultvalue cselect**"; print_r($this->default_value );
        //echo "<br>Postedvalue cselect**";  print_r($this->posted_value) ;
        
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
    
    
    public function byla_zmena_hodnoty() {
        
        //echo "<br><br>FFFfce zjistovani zda byla zmena hodnoty cselect";
        //echo "<br>old " . $this->old_value;
        //echo "<br>posted " . $this->posted_value. "<br>";
        
        if ($this->posted_value) {
             if ($this->old_value == $this->posted_value) {                
                 return false; }
             else {return true; }
        }
        else {return false;}
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
        //Získání položek z číselníku
        // SV: Volá se i z webform_cselect_multi a pak je $this->old_value array (někdy také prázdné)
        if (is_array($this->old_value) AND $this->old_value) {
            $sql_query = 'SELECT kod,text,plny_text,deleted FROM '.$this->ciselnik_db_name.' WHERE deleted=0 OR kod IN ('.implode(', ', $this->old_value).') ORDER BY razeni ASC';            
        } else {
            $sql_query = 'SELECT kod,text,plny_text,deleted FROM '.$this->ciselnik_db_name.' WHERE deleted=0 OR kod="'.($this->old_value ? $this->old_value : '').'" ORDER BY razeni ASC';
        }
        $res=$this->mdb2->query($sql_query);
        if($res->rowCount()) {
            //Zobrazení prvku
            if($parts=='ALL' or $parts=='DESC') {
    
                //echo htmlspecialchars($this->item_description,ENT_QUOTES)." \n"; // *SEL*
                echo $this->item_description." \n";  
                
            }
            if($parts=='ALL' or $parts=='INPUT') {
                echo '<SELECT NAME="'.$this->item_name.'" ID="'.$this->item_name.'" SIZE="'.$this->item_size.'" '.webform_styles::$cselect.'>'."\n";
                if($this->posted_value=='' AND $this->default_value=='') {
                    echo '<OPTION VALUE="----" SELECTED> --- Vyberte ---'."\n";
                }
                while (($row = $res->fetch(\PDO::FETCH_ASSOC))) {
                    echo '<OPTION VALUE="'.$row['kod'].'" TITLE="'.htmlspecialchars($row['plny_text'],ENT_QUOTES).'"';
                    //echo "zaznam:".$row['kod']."\t posted:".$this->posted_value."\t default:".$this->default_value."\n";
                    if($row['kod']==$this->posted_value or ($row['kod']==$this->default_value and $this->posted_value=='')) {
                        echo ' SELECTED';
                    }
                    if($row['deleted']){
                        echo '>!!-'.htmlspecialchars($row['text'],ENT_QUOTES).'-!!'."\n";
                    }
                    else {
                        echo '>'.htmlspecialchars($row['text'],ENT_QUOTES)."\n";
                    }
                   //print_r($row);
                    
                } //while
                echo '</SELECT>'."\n";
                
                
                if (($this->item_name == "sub_dalsi_kurzy") or ($this->item_name == "sub_moduly_kurzu")) {
                    // sub_dalsi_kurzy, sub_moduly_kurzu jsou selecty pro multiselect a v tech se nenabizi vymazane
                    }
                else{
                   echo '<span '. webform_styles::$drobna_poznamka .'>&nbsp;&nbsp;!!- &nbsp;&nbsp; označuje, že položka je znepřístupněna<br></span>';
                }
                
                //echo '<span '. webform_styles::$drobna_poznamka .'>' . $this->item_name . '</span>';     // *SEL* ladici
                
                if($this->error_bool) {
                    echo '<SPAN '.webform_styles::$error.'><BR>Chyba: '.$this->error_text.'</SPAN>'."\n";
                }
                echo "\n";
            }
            
            if($parts=='JEN_KOD') {
                 echo "<b>" .$this->old_value ."</b> \n";  
            }
        }
    }//function Display
    
    
} //class c_webform_cselect

?>
