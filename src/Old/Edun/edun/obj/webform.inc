<?php
/**
 * Depository for web form objects
 *
 * @name webform
 * @author
 * @version 1.0
 * @package webform_obj
 */
require_once Middleware\Edun\AppContext::getScriptsDirectory()."edun/obj/webform_styles.inc";

/**
 *Třída webform je určena k rozšíření o vlastní položky formuláře
 *Zajišťuje zobrazení tagu <FORM> nastavení skriptu zpracujícího data a
 *zobrazení tlačítek ke smazání a uložení formuláře
 */
class webform {
    private $name;
    private $action_script;
    private $path_to_app;

    /**
     * Constructor
     * @param string $name Název pod kterým bude formulář vložen na stránku
     * @param string $action_script Skript, který bude zpracovávat data z formuláře !!Nastavit na stránku na které bude formulář zobrazen, položky načítají data samy
     * @access  public
     */
    public function __construct($name,$action_script) {
        $this->name = $name;
        $this->action_script = $action_script;
        //TODO: Svoboda - provizorně upravená třída webform - přidáno získání cesty ke složce s js skripty z AppContext
        // nově - absolutní adresa:
        $this->path_to_app = Middleware\Edun\AppContext::getAppPublicDirectory();
    }

    public function Display($part) {
        switch($part) {
            case 'top':

                echo '<FORM name="'.$this->name.'" id="'.$this->name.
                     '" action="'.$this->action_script.
                     '" method="POST" onSubmit= "return pri_odeslani();" '.webform_styles::$form.'>';
                echo '<script type ="text/javascript" src="'.$this->path_to_app.'obj/form.js"></script>'."\n";
                break;
            case 'buttons':
                echo '<INPUT type="SUBMIT" name="OK" '.webform_styles::$input_submit.'> ';
                echo '<INPUT type="RESET" name="CLEAR" '.webform_styles::$input_reset.'> ';
                break;
            case 'bottom':
                echo '<script type ="text/javascript">'."\n";
                echo 'init()'."\n";
                echo 'var form=document.getElementById("'.$this->name.'")'."\n";
                echo 'form.onreset=init'."\n";
                echo 'pri_natazeni()' . "\n";
                echo '</script>'."\n";
                echo '</FORM>';

                break;
        }
    }
}

class id {
    private $id;
    private $item_name;
    private $sql_date;

    public function __construct($item_name,$id=false,$sql_date=false) {
        $this->item_name=$item_name;
        $this->id = $id;
        $this->sql_date = $sql_date;
    }

    public function Display() {
        if($this->item_name) {
            echo '<INPUT type="hidden" name="'.$this->item_name.'" id="'.$this->item_name.'" value="'.id_code::code($this->id,$this->sql_date).'" readonly>'."\n";
        }
    }

    public function Get_value() {
        return $this->id;
    }
}




class id_code {

    public static function code($id,$updated) {
        if ( ($updated==false) ) {
            if ($id==false) {$id=0;}
            return $id;
        }

        $timestamp = strtotime($updated);
        $id_position = substr($timestamp,strlen($timestamp)-1);
        $id_digits = strlen($id);
        return substr($timestamp,0,$id_position).$id.substr($timestamp,$id_position).$id_digits;
    }

    public static function decode($code) {
        $id_digits=substr($code,strlen($code)-1);
        $id_position = substr($code,strlen($code)-2,1);
        return substr($code,$id_position,$id_digits);
    }


}
?>