<?php
/**
 * Depository for web form objects
 * 
 * @name webform_input_checkbox
 * @author
 * @version 1.0
 * @package webform_obj
 */
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_styles.inc";



/**
 *Třída c_webform_checkbox slouží k zobrazení checkboxů ve www formuláři.
 *Checkboxy jsou generovány na základě zadané tabulky v číselníku
 *Po odeslání validuje vrácené pole hodnot
 *Je možné přednastavit které checkboxy budou na začátku zaškrtnuté
 */
class c_webform_input_checkbox {
    private $item_name;
    private $item_description;
    private $number_of_collumns;
    private $ciselnik_db_name;
    private $old_value = false;
    private $default_value = false;
    private $error_bool;
    private $error_text;
    private $posted_value;
    private $mdb2;
    /**
     *Constructor
     *@param string $item_name Název pod kterým bude položka vložena do formuláře
     *@param string $item_description Text který bude zobrazen u položky ve formuláři uživateli
     *@param int $item_size Počet řádků které se zobrazí v položce
     *@param string $ciselnik_db_name Název tabluky s položkami
     *@param string $default_value Kód výchozí hodnoty čísleníku, pokud je uveden, bude předvybrána příslušná hodnota,
     * pokud je uveden neexistující, zobrazí se jako výchozí hodnota první dle seznamu, pokud není uvedena, nebo pokud je
     * je jako kod nastaven prázdný řetězec '' tak se zobrazí výzva k výběru
     *@param string $old_value Kód pro přednastavení hodnoty při editaci formuláře, je možné nastavit i kód smazaných položek
     *(ve sloupci deleted =1) tak aby mohl být znovu uložen formulář s původními třeba již smazanými položkami číselníku
     * @access  public
    */
    public function __construct($item_name,$item_description,$number_of_collumns,$ciselnik_db_name,$old_value='') {
        $this->item_name=$item_name;
        $this->item_description=$item_description;
        $this->number_of_collumns=$number_of_collumns;
        $this->ciselnik_db_name=$ciselnik_db_name;
        $this->old_value=$old_value;
        $this->mdb2 = Middleware\Edun\AppContext::getDb();
        
        //Načtení hodnoty z pole _POST
        if( isset($_POST[$this->item_name])) {
            $this->posted_value=array();
            if(is_array($_POST[$this->item_name])){
                $this->posted_value=$_POST[$this->item_name];
                $posted_value_string = implode('","',$this->posted_value);
            }
            else {
                $posted_value_string='';
            }
            if (is_array($this->old_value)){
                $old_value_string=implode('","',$this->old_value);
            }
            else {
                $old_value_string=$this->old_value;
            }
            $sql_query = 'SELECT kod,text FROM '.$this->ciselnik_db_name.' WHERE kod IN ("'.$posted_value_string.'")
                                AND (deleted=0 OR kod IN ("'.$old_value_string.'")) ORDER BY razeni ASC';
            //echo $sql_query;
            $res=$this->mdb2->query($sql_query);
            if($res->rowCount()<=count($this->posted_value)) {
                $this->error_bool=false;
                $this->error_text='';
            }
            else{
                $this->error_bool=true;
                $this->error_text='Některé volby již nejsou platné, zkontrolujte vaší volbu!';
                $this->posted_value=array();
                while($row = $res->fetch(\PDO::FETCH_ASSOC)){
                    array_push($this->posted_value,$row['kod']);
                }
            }
        }
         
        //echo "<br><br>construct Itemname checkbox**" . $this->item_name ;   // *SEL*
        //echo "<br>Oldvalue checkbox**" . $this->old_value ;
        //echo "<br>Defaultvalue checkbox**" . $this->default_value ;
        //echo "<br>Postedvalue checkbox**" . $this->posted_value ;
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
        //Získání položek z číselníku
        $sql_query = 'SELECT kod,text,plny_text,deleted FROM '.$this->ciselnik_db_name.' WHERE deleted=0 OR kod IN ("'.@implode('","',$this->old_value).'") ORDER BY razeni ASC';
        $res=$this->mdb2->query($sql_query);
        if($res->rowCount()) {
            //Pokud nejsou zaslány hodnoty v poli _POST, tedy při zobrazení prázdného formuláře, pak se kod vyhledava v puvodních hodnotách
            if(is_array($this->posted_value)){
                $array_to_search_in=$this->posted_value; //Nové zaslané hodnoty
            }
            elseif (is_array($this->old_value)){
                $array_to_search_in=$this->old_value; //Původní hodnoty
            }
            elseif (is_array($this->default_value)){
                $array_to_search_in=$this->default_value; //Předvolené hodnoty
            }
            else {
                $array_to_search_in=array();  //Nic nebude zaškrtnuto
            }
            //Zobrazení prvku
            //echo '<INPUT type="hidden"  name="'.$this->item_name.'" value="">'."\n";   
            //echo '*formularove pole ' . $this->item_name . '*hidden:';
            echo  '<INPUT type="text"  name="'.$this->item_name.'" value="" hidden readonly>'."\n"; echo "<br>"; // *SEL*
            
            //echo "<BR>*old_value* " ; print_r($this->old_value) ; echo "<BR>"; // *SEL* 
            //echo  '* '. implode('","', $this->old_value ) .'*<BR>'; // *SEL* 
                       
            if($parts=='ALL' or $parts=='DESC') {
                echo htmlspecialchars($this->item_description,ENT_QUOTES).'<br>'."\n";
            }
            if($parts=='ALL' or $parts=='INPUT') {
                echo '<table    border="0"
                                width="100%" '.
                                webform_styles::$input_checkbox.'>';
                $collumns_width=100/$this->number_of_collumns;                            
                do{
                    echo '<TR>';
                    for($i = 0;$i<$this->number_of_collumns;$i++){
                        $row = $res->fetch(\PDO::FETCH_ASSOC);
                        echo '<TD width="'.$collumns_width.'%">';
                        if ($row) {
                            echo '<INPUT    type="Checkbox"
                                            name="'.htmlspecialchars($this->item_name,ENT_QUOTES).'[]"
                                            value="'.$row['kod'].'"';
    
                            //if(@array_search($row['kod'],$this->posted_value) or @array_search($row['kod'],$this->old_value)){
                            if(@in_array($row['kod'],$array_to_search_in)){ //Zaškrtne políčko pokud najde kód ve vyhledávacím poli
                                echo'       checked="checked">';        
                            }
                            else {
                                echo'       >';
                            }
                            if ($row['deleted']) {    /* zvyrazni, kdyz polozka ciselniku je mezitim smazana*/
                                echo "<BR>" . '!!-'  .  htmlspecialchars($row['text'],ENT_QUOTES)  . '-!!';
                            }                           
                            else    {
                                echo htmlspecialchars($row['text'],ENT_QUOTES);
                            }    
                        }
                        else {
                            echo '&nbsp';
                        }
                        echo '</TD>';
                    }
                    echo '</TR>'."\n";
                } while($row);
                echo '</table>'."\n";
                echo '<span '. webform_styles::$drobna_poznamka .'>&nbsp;&nbsp;!!- &nbsp;&nbsp; označuje, že položka je znepřístupněna</span><br>';
               
                //echo '<span '. webform_styles::$drobna_poznamka .'>' . $this->item_name . '</span>';   // *SEL* ladici
                if($this->error_bool) {
                    echo '<SPAN '.webform_styles::$error.'><BR>Chyba: '.$this->error_text.'</SPAN>'."\n";
                }
            }
        }
    }
}


?>
