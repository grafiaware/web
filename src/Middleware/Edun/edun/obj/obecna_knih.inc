<?php


//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
/**
* Obecná knihovna.
*
* @package synchro
*/
//-------------------------------------------------------------------------------
class Obecna_Knih {

    
  public static function check_date_cele ($str) {
    if (!ereg('^[[0-9]{1,2}[\.][0-9]{1,2}[\.][0-9]{4}$',$str)) return FALSE;
    list ($d,$m,$r) = split('\.',$str );   
    return checkdate($m,$d,$r);
  }  //check_date_cele


  /**
  * Kontrola datumu
  *
  * Vezme ze vstupního řetězce prvních 10 znaků a pomocí funkce check_date_cele 
  * zkontroluje, zda se jedná o validní datum. - oddelovac pouze tecka
  * 
  * @param string $dat kontrolovaný řetězec
  * @return boolean $OK  true = datum je OK, false = špatné datum
  */
  public static function kontrola_datum ($dat) {
        $d = substr($dat,0,10);
       
        $bol  = self::check_date_cele($d);
        if  ($bol)  {
            $OK=true;    
        }  
        else {
            $OK=false;   
        }
   
        return $OK ;
  } // function kontrola_datum


 /**
 *Priprava datumu pro ulozeni  do db
 *
 *@param string $sdatum  datum ve tvaru dd.mm.yyyy
 */
 public static function priprav_datum_dodb ($sdatum) {
            if ($sdatum) {
              list($den, $mesic, $rok) = split('[.]', $sdatum );
              $DatOddataPlan = mktime(0,0,0, $mesic,$den,$rok);
              $dateDatOddataPlan = '\'' . date("Y-m-d",$DatOddataPlan) . '\'';
            }
            else {$dateDatOddataPlan="NULL";}
 return $dateDatOddataPlan ;         
  }
  
  
 /**
 *Konverze řetězce datumu z yyyy-mm-dd na dd.mm.yyyy
 *
 *@param string $sdatum  datum ve tvaru yyyy-mm-dd
 *@return string  datum ve tvaru dd.mm.yyyy, nebo "" (prázdný řetězec)
 *
 */  
  public static function datum_ymd_dmy ($sdatum=false) {
     if ($sdatum) {
              $dpom = substr($sdatum,8,2) .".".  substr($sdatum,5,2) ."." .substr($sdatum,0,4) ;
     }
     else {$dpom = "";}
  return $dpom ;  
  }
  
  
  public static function datum_ymd_dmy2 ($sdatum=false) {
     if ($sdatum) {
              $dpom = substr($sdatum,8,2) .".".  substr($sdatum,5,2) ."." .substr($sdatum,2,2) ;
     }
     else {$dpom = "";}
  return $dpom ;  
  }
  
  
  

}

?>