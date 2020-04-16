<?php
 function check_date_cele ($str) {
    if (!ereg('^[[0-9]{1,2}[\.][0-9]{1,2}[\.][0-9]{4}$',$str)) return FALSE;
    list ($d,$m,$r) = split('\.',$str );   
    return checkdate($m,$d,$r);
 }  //check_date_cele
  
 function check_date_rok ($str) {
    if (!ereg("[19|20][0-9]{2}",$str)) {return FALSE;}
    else { return TRUE;}
 }  //check_date_rok
  
  /**
  * Vezme ze vstupního řetězce prvních 10 znaků a pomocí funkce check_date_cele 
  * zkontroluje, zda se jedná o validní datum. - oddelovac pouze tecka
  * 
  * @param string $dat kontrolovaný řetězec
  * @return boolean true = datum je OK, false = špatné datum
  */
  function kontrola_datum ($dat) {
        $d = substr($dat,0,10); 
        $bol  = check_date_cele($d);
        if  ($bol) {$OK=true;}  
        else {$OK=false;} 
   
        return $OK ;
  } // function kontrola_datum



  /**
  * Vezme ze vstupního řetězce první 4 znaků a pomocí funkce check_date_rok
  * zkontroluje, zda se jedná o validní datum. 
  * 
  * @param string $dat kontrolovaný řetězec
  * @return boolean true = datum je OK, false = špatné datum
  */
  function kontrola_datum_rok ($dat) {
       $d = substr($dat,0,4); 
       $bol  = check_date_rok($d);
       if ($bol) {$OK=true;}  
       else {$OK=false;} 
   
       return $OK ;
  } // function kontrola_datum_rok


 /**
  * Vezme ze vstupního řetězce prvních 50 znaků a pomocí regul.výrazu 
  * zkontroluje, zda se jedná o validní mail.adresu. 
  * 
  * @param string $text kontrolovaný řetězec
  * @return boolean true = je OK, false = není OK
  */
  function kontrola_email ($text) {
       $t = substr($text,0,50);
        
       if (!(ereg('^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)+$', $t) )) {$OK=FALSE; }
       //if (!(ereg('@[^@]+[.][a-zA-Z]+$', $t) ) )  {$OK=FALSE; } 
       else  {$OK=TRUE;}

       return $OK ;
  } // function kontrola_email


  /**
  * Vezme ze vstupního řetězce prvních 5 znaků a pomocí regul.výrazu 
  * zkontroluje, zda se jedná o validní pošt.směrovací číslo. 
  * 
  * @param string $text kontrolovaný řetězec
  * @return boolean true = je OK, false = není OK
  */
  function kontrola_psc ($text) {
       $t = substr($text,0,5);
        
       if (!(ereg('[0-9]{5}' , $t) )) {$OK=FALSE; }
       else  {$OK=TRUE;}
   
       return $OK ;
  } // function kontrola_psc
  
  
  
   /**
  * Vezme ze vstupního řetězce prvních 10 znaků a pomocí regul.výrazu 
  * zkontroluje, zda se jedná o validní číslo domu. 
  * 
  * @param string $text kontrolovaný řetězec
  * @return boolean true = je OK, false = není OK
  */
  function kontrola_cislo_domu ($text) {
       $t = substr($text,0,10);
        
       if (!(ereg("^[-a-zA-Z0-9\/]{1,10}$" , $t) )) {$OK=FALSE; }
       else  {$OK=TRUE;}
   
       return $OK ;
  } // function kontrola_cislo_domu


  /**
  * Vezme ze vstupního řetězce prvních 40 znaků a pomocí regul.výrazu 
  * zkontroluje. 
  * 
  * @param string $text kontrolovaný řetězec
  * @return boolean true = je OK, false = není OK
  */
  function kontrola_telefon ($text) {
       $t = substr($text,0,40);
        
       if (!(ereg("^[0-9\.\+\/\-]{9,}$" , $t) )) {$OK=FALSE; }
       else  {$OK=TRUE;}
   
       return $OK ;

  } // function kontrola_telefon
  
  
//-----------------------------------------------------------------------------  
  

function text10_ ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola true|false, [3] - název funkce
// $krok- určuje, co udělat:
//1-jen zobrazení bez kontroly,
//2-trim, kontrola, pri OK - zkrati,upravi [1], nastavi[2] 1, zobrazi hidden upravené hodnoty,
//                    pri neOK - nastavi [2] 0, zobrazi chybu (zde nenastava, povoleno vse)
//ostatní-trim, kontrola, nastavi[2] popr[1] jako u kroku 2
// Funkce vrací upravené pole $hodnoty (upraví [1],[2]).
//
// Pole  je text, nepovinné, ořízne na délku 10
//       
  Switch ($krok){
  case 1:
    echo $hodnoty[0].': <input type="text" name="'.$klic.'" value="'.$hodnoty[1].
                         '" class="text" maxlength="10">';
  break;
  case 2:
    $hodnoty[1] = trim ($hodnoty[1]);
    
      $hodnoty[1] = substr($hodnoty[1], 0, 10);
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
      
      $hodnoty[2] = 1;
      echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      echo '<input  name="'. $klic .'" type="hidden"  value="'.$hodnoty[1].'">';   //type="hidden"
    
  break;
  default:
    $hodnoty[1] = trim ($hodnoty[1]);
      
      $hodnoty[2] = 1;
      $hodnoty[1] = substr($hodnoty[1], 0, 50);
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
       
  }
  return $hodnoty;
} //function text10_




function text50 ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola true|false, [3] - název funkce
// $krok- určuje, co udělat:
//1-jen zobrazení bez kontroly,
//2-trim, kontrola, pri OK - zkrati,upravi [1], nastavi[2] 1, zobrazi hidden upravené hodnoty,
//                    pri neOK - nastavi [2] 0, zobrazi chybu
//ostatní-trim, kontrola, nastavi[2] popr[1] jako u kroku 2
// Funkce vrací upravené pole $hodnoty (upraví [1],[2]).
//
// Pole  je text, povinné, ořízne na délku 50
//         
  Switch ($krok){
  case 1:
    echo $hodnoty[0].': <input type="text" name="'.$klic.'" value="'.$hodnoty[1].
                         '" class="text" maxlength="50">';
  break;
  case 2:
    $hodnoty[1] = trim ($hodnoty[1]);
    
    IF (strlen($hodnoty[1])<2)
    {
      echo $hodnoty[0].': <span class="chyba_staffer">Nevyplněné povinné pole!</span>';
      $hodnoty[2] = 0;
    }
    ELSE {
      $hodnoty[1] = substr($hodnoty[1], 0, 50);
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
      $hodnoty[2] = 1;
      
      echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      echo '<input  name="'. $klic .'"  type="hidden"  value="'.$hodnoty[1].'">'; //type="hidden"
    }
  break;
  default:
      $hodnoty[1] = trim ($hodnoty[1]); 
      IF (strlen($hodnoty[1])<2)
      {
        $hodnoty[2]=0;
      }
      ELSE {
        $hodnoty[2] = 1;
        $hodnoty[1] = substr($hodnoty[1], 0, 50);
        $hodnoty[1] = strip_tags ($hodnoty[1]);
        $hodnoty[1] = addslashes($hodnoty[1]);
      } 
  }
  return $hodnoty;
} //function text50


function text100 ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola true|false, [3] - název funkce
// $krok- určuje, co udělat:
//1-jen zobrazení bez kontroly,
//2-trim, kontrola, pri OK - zkrati,upravi [1], nastavi[2] 1, zobrazi hidden upravené hodnoty,
//                    pri neOK - nastavi [2] 0, zobrazi chybu
//ostatní-trim, kontrola, nastavi[2] popr[1] jako u kroku 2
// Funkce vrací upravené pole $hodnoty (upraví [1],[2]).
//
// Pole  je text, povinné, ořízne na délku 100
//     
  Switch ($krok){
  case 1:
    echo $hodnoty[0].': <input type="text" name="'.$klic.'" value="'.$hodnoty[1].
                        '" class="text" maxlength="100">';
  break;
  case 2:
    $hodnoty[1] = trim ($hodnoty[1]);
    
    IF (strlen($hodnoty[1])<2)
    {
      echo $hodnoty[0].': <span class="chyba_staffer">Nevyplněné povinné pole!</span>';
      $hodnoty[2] = 0;
    }
    ELSE {
      $hodnoty[1] = substr($hodnoty[1], 0, 100);
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
      $hodnoty[2] = 1;
      echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      echo '<input  name="'. $klic .'"  type="hidden" value="'.$hodnoty[1].'">'; //type="hidden"
    }
  break;
  default:
      $hodnoty[1] = trim ($hodnoty[1]); 
      IF (strlen($hodnoty[1])<2)
      {
        $hodnoty[2]=0;
      }
      ELSE {
        $hodnoty[2] = 1;
        $hodnoty[1] = substr($hodnoty[1], 0, 100);
        $hodnoty[1] = strip_tags ($hodnoty[1]);
        $hodnoty[1] = addslashes($hodnoty[1]);
      } 
  }
  return $hodnoty;
}//function text100




function text255_ ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola true|false, [3] - název funkce
// $krok- určuje, co udělat:
//1-jen zobrazení bez kontroly,
//2-trim, kontrola, pri OK - zkrati,upravi [1], nastavi[2] 1, zobrazi hidden upravené hodnoty,
//                    pri neOK - nastavi [2] 0, zobrazi chybu (zde nenastava, povoleno vse)
//ostatní-trim, kontrola, nastavi[2] popr[1] jako u kroku 2
// Funkce vrací upravené pole $hodnoty (upraví [1],[2]).
//
// Pole  je text, nepovinné, ořízne na délku 255
//       
  Switch ($krok){
  case 1:
    echo $hodnoty[0].': <input type="text" name="'.$klic.'" value="'.$hodnoty[1].
                         '" class="text" maxlength="255">';
  break;
  case 2:
    $hodnoty[1] = trim ($hodnoty[1]);
    
      $hodnoty[1] = substr($hodnoty[1], 0, 255);
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
      
      $hodnoty[2] = 1;
      echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      echo '<input  name="'. $klic .'" type="hidden"  value="'.$hodnoty[1].'">'; //type="hidden"
    
  break;
  default:
    $hodnoty[1] = trim ($hodnoty[1]);
      
      $hodnoty[2] = 1;
      $hodnoty[1] = substr($hodnoty[1], 0, 255);
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
       
  }
  return $hodnoty;
} //function text255_





function poznamka_ ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola true|false, [3] - název funkce
// $krok- určuje, co udělat:
//1-jen zobrazení bez kontroly,
//2-trim, kontrola, pri OK - zkrati,upravi [1], nastavi[2] 1, zobrazi hidden upravené hodnoty,
//                    pri neOK - nastavi [2] 0, zobrazi chybu (zde nenastava, povoleno vse)
//ostatní-trim, kontrola, nastavi[2] popr[1] jako u kroku 2
// Funkce vrací upravené pole $hodnoty (upraví [1],[2]).
//
// Pole  je text, povinné, ořízne na délku 800
//       
  Switch ($krok){
  case 1:
    echo $hodnoty[0].': <textarea cols="85" rows="10" class="txtpole"  name="' . $klic .
          '">' . $hodnoty[1] .  '</textarea>' ;
  break;
  case 2:
    $hodnoty[1] = trim ($hodnoty[1]);
    
      $hodnoty[1] = substr($hodnoty[1], 0, 800);
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
      
      $hodnoty[2] = 1;
      echo $hodnoty[0].': <textarea cols="85" rows="10" class="txtpole" type="hidden" name="' . $klic .
           '">' . $hodnoty[1] .  '</textarea>' ;    //type="hidden"
    
  break;
  default:
      $hodnoty[1] = trim ($hodnoty[1]);
      
        $hodnoty[2] = 1;
        $hodnoty[1] = substr($hodnoty[1], 0, 800);
        $hodnoty[1] = strip_tags ($hodnoty[1]);
        $hodnoty[1] = addslashes($hodnoty[1]);
      } 
  
  return $hodnoty;
} //function poznamka_










function narozen_ ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola true|false, [3] - název funkce
// $krok- určuje, co udělat:
//1-jen zobrazení bez kontroly,
//2-trim, kontrola, pri OK - zkrati,upravi [1], nastavi[2] 1, zobrazi hidden upravené hodnoty,
//                    pri neOK - nastavi [2] 0, zobrazi chybu
//ostatní-trim, kontrola, nastavi[2] popr[1] jako u kroku 2
// Funkce vrací upravené pole $hodnoty (upraví [1],[2]).
//
// Pole  je text, nepovinné, ořízne na délku 10
//       
  Switch ($krok){
  case 1:
    echo $hodnoty[0].': <input type="text" name="'.$klic.'" value="'.$hodnoty[1].
                         '" class="text" maxlength="10">';
  break;
  case 2:
    $hodnoty[1] = trim ($hodnoty[1]);
    
    $O = kontrola_datum($hodnoty[1]);
    if ($hodnoty[1]=="") {$O = true;}
    
    IF ( !($O)  )     
    {
      echo $hodnoty[0].': <span class="chyba_staffer">Nevyhovující datum narození!</span>';
      $hodnoty[2] = 0;
    }
    ELSE {
      $hodnoty[1] = substr($hodnoty[1], 0, 10);
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
      $hodnoty[2] = 1;
      echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      echo '<input  name="'. $klic .'"  type="hidden" value="'.$hodnoty[1].'">'; //type="hidden"
    }
  break;
  default:
      $hodnoty[1] = trim ($hodnoty[1]);
      
      $O = kontrola_datum($hodnoty[1]);
      if ($hodnoty[1]=="") {$O = true;}
      
      IF (!( $O ) )   
      {
        $hodnoty[2]=0;
      }
      ELSE {
        $hodnoty[2] = 1;
        $hodnoty[1] = substr($hodnoty[1], 0, 10);
        $hodnoty[1] = strip_tags ($hodnoty[1]);
        $hodnoty[1] = addslashes($hodnoty[1]);
      } 
  }
  return $hodnoty;
} //function narozen_




function datum_ ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola true|false, [3] - název funkce
// $krok- určuje, co udělat:
//1-jen zobrazení bez kontroly,
//2-trim, kontrola, pri OK - zkrati,upravi [1], nastavi[2] 1, zobrazi hidden upravené hodnoty,
//                    pri neOK - nastavi [2] 0, zobrazi chybu
//ostatní-trim, kontrola, nastavi[2] popr[1] jako u kroku 2
// Funkce vrací upravené pole $hodnoty ([1],[2]).
//
// Pole  je text, nepovinné, ořízne na délku 10 nebo 4
//       
  Switch ($krok){
  case 1:
    echo $hodnoty[0].': <input type="text" name="'.$klic.'" value="'.$hodnoty[1].
                         '" class="text" maxlength="10">';
  break;
  case 2:
    $hodnoty[1] = trim ($hodnoty[1]);
    
    if (strlen( $hodnoty[1]) < 5) {
        $O = kontrola_datum_rok($hodnoty[1]);
    }
    else{
        $O = kontrola_datum($hodnoty[1]);
    }
    if ($hodnoty[1]=="") {$O = true;}
    
    IF ( !( $O ) )      {
      echo $hodnoty[0].': <span class="chyba_staffer">Nevyhovující datum narození!</span>';
      $hodnoty[2] = 0;
    }
    ELSE {
      if (strlen( $hodnoty[1]) < 8){
            $hodnoty[1] = substr($hodnoty[1], 0, 4);
      }
      else{
            $hodnoty[1] = substr($hodnoty[1], 0, 10);
      }      
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
      $hodnoty[2] = 1;
      echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      echo '<input  name="'. $klic .'"  type="hidden"  value="'.$hodnoty[1].'">'; //type="hidden"
    }
  break;
  default:
      $hodnoty[1] = trim ($hodnoty[1]);
      
      if (strlen( $hodnoty[1]) < 5) {
        $O = kontrola_datum_rok($hodnoty[1]); }
      else{
        $O = kontrola_datum($hodnoty[1]); }
      if ($hodnoty[1]=="") {$O = true;}  
      
      IF ( !($O) )      {
        $hodnoty[2]=0;
      }
      ELSE {
        $hodnoty[2] = 1;
        if (strlen( $hodnoty[1]) < 5) {
            $hodnoty[1] = substr($hodnoty[1], 0, 4);
        }
        else {
            $hodnoty[1] = substr($hodnoty[1], 0, 10);
        }  
        $hodnoty[1] = strip_tags ($hodnoty[1]);
        $hodnoty[1] = addslashes($hodnoty[1]);
      } 
  }
  return $hodnoty;
} //function datum_




function cislo_domu ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola true|false, [3] - název funkce
// $krok- určuje, co udělat:
//1-jen zobrazení bez kontroly,
//2-trim, kontrola, pri OK - zkrati,upravi [1], nastavi[2] 1, zobrazi hidden upravené hodnoty,
//                    pri neOK - nastavi [2] 0, zobrazi chybu
//ostatní-trim, kontrola, nastavi[2] popr[1] jako u kroku 2
// Funkce vrací upravené pole $hodnoty (upraví [1],[2]).
//
// Pole  je text, povinné, ořízne na délku 10
//       
  Switch ($krok){
  case 1:
    echo $hodnoty[0].': <input type="text" name="'.$klic.'" value="'.$hodnoty[1].
                         '" class="text" maxlength="10">';
  break;
  case 2:
    $hodnoty[1] = trim ($hodnoty[1]);
    
    IF (!( kontrola_cislo_domu($hodnoty[1])) )     
    {
      echo $hodnoty[0].': <span class="chyba_staffer">Nevyhovující číslo domu!</span>';
      $hodnoty[2] = 0;
    }
    ELSE {
      $hodnoty[1] = substr($hodnoty[1], 0, 10);
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
      
      $hodnoty[2] = 1;
      echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      echo '<input  name="'. $klic .'"  type="hidden" value="'.$hodnoty[1].'">'; //type="hidden"
    }
  break;
  default:
      $hodnoty[1] = trim ($hodnoty[1]);
      
      IF ( !(kontrola_cislo_domu($hodnoty[1]))  )    
      {
        $hodnoty[2]=0;
      }
      ELSE {
        $hodnoty[2] = 1;
        $hodnoty[1] = substr($hodnoty[1], 0, 10);
        $hodnoty[1] = strip_tags ($hodnoty[1]);
        $hodnoty[1] = addslashes($hodnoty[1]);
      } 
  }
  return $hodnoty;
} // function cislo_domu





function psc ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola true|false, [3] - název funkce
// $krok- určuje, co udělat:
//1-jen zobrazení bez kontroly,
//2-trim, kontrola, pri OK - zkrati,upravi [1], nastavi[2] 1, zobrazi hidden upravené hodnoty,
//                    pri neOK - nastavi [2] 0, zobrazi chybu
//ostatní-trim, kontrola, nastavi[2] popr[1] jako u kroku 2
// Funkce vrací upravené pole $hodnoty (upraví [1],[2]).
//
// Pole  je text, povinné, ořízne na délku 5
//       
  Switch ($krok){
  case 1:
    echo $hodnoty[0].': <input type="text" name="'.$klic.'" value="'.$hodnoty[1].
                         '" class="text" maxlength="5">';
  break;
  case 2:
    $hodnoty[1] = trim ($hodnoty[1]);
    
    IF (!( kontrola_psc($hodnoty[1])) )     
    {
      echo $hodnoty[0].': <span class="chyba_staffer">Nevyhovující PSČ!</span>';
      $hodnoty[2] = 0;
    }
    ELSE {
      $hodnoty[1] = substr($hodnoty[1], 0, 5);
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
      
      $hodnoty[2] = 1;
      echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      echo '<input  name="'. $klic .'"  type="hidden" value="'.$hodnoty[1].'">'; //type="hidden"
    }
  break;
  default:
      $hodnoty[1] = trim ($hodnoty[1]);
      
      IF ( !(kontrola_psc($hodnoty[1]))  )    
      {
        $hodnoty[2]=0;
      }
      ELSE {
        $hodnoty[2] = 1;
        $hodnoty[1] = substr($hodnoty[1], 0, 5);
        $hodnoty[1] = strip_tags ($hodnoty[1]);
        $hodnoty[1] = addslashes($hodnoty[1]);
      } 
  }
  return $hodnoty;
} // function psc




function telefon ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola true|false, [3] - název funkce
// $krok- určuje, co udělat:
//1-jen zobrazení bez kontroly,
//2-trim, kontrola, pri OK - zkrati,upravi [1], nastavi[2] 1, zobrazi hidden upravené hodnoty,
//                    pri neOK - nastavi [2] 0, zobrazi chybu
//ostatní-trim, kontrola, nastavi[2] popr[1] jako u kroku 2
// Funkce vrací upravené pole $hodnoty (upraví [1],[2]).
//
// Pole  je text, povinné, ořízne na délku 40
//       
  Switch ($krok){
  case 1:
    echo $hodnoty[0].': <input type="text" name="'.$klic.'" value="'.$hodnoty[1].
                         '" class="text" maxlength="40">';
  break;
  case 2:
    $hodnoty[1] = trim ($hodnoty[1]);
    
    IF (!( kontrola_telefon($hodnoty[1])) )     
    {
      echo $hodnoty[0].': <span class="chyba_staffer">Nevyhovující číslo telefonu!</span>';
      $hodnoty[2] = 0;
    }
    ELSE {
      $hodnoty[1] = substr($hodnoty[1], 0, 40);
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
      
      $hodnoty[2] = 1;
      echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      echo '<input  name="'. $klic .'"  type="hidden" value="'.$hodnoty[1].'">'; //type="hidden"
    }
  break;
  default:
      $hodnoty[1] = trim ($hodnoty[1]);
      
      IF ( !(kontrola_telefon($hodnoty[1]))  )    
      {
        $hodnoty[2]=0;
      }
      ELSE {
        $hodnoty[2] = 1;
        $hodnoty[1] = substr($hodnoty[1], 0, 40);
        $hodnoty[1] = strip_tags ($hodnoty[1]);
        $hodnoty[1] = addslashes($hodnoty[1]);
      } 
  }
  return $hodnoty;
} // function telefon





function email ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola true|false, [3] - název funkce
// $krok- určuje, co udělat:
//1-jen zobrazení bez kontroly,
//2-trim, kontrola, pri OK - zkrati,upravi [1], nastavi[2] 1, zobrazi hidden upravené hodnoty,
//                    pri neOK - nastavi [2] 0, zobrazi chybu
//ostatní-trim, kontrola, nastavi[2] popr[1] jako u kroku 2
// Funkce vrací upravené pole $hodnoty (upraví [1],[2]).
//
// Pole  je text, povinné, ořízne na délku 50
//       
  Switch ($krok){
  case 1:
    echo $hodnoty[0].': <input type="text" name="'.$klic.'" value="'.$hodnoty[1].
                         '" class="text" maxlength="50">';
  break;
  case 2:
    $hodnoty[1] = trim ($hodnoty[1]);
    
    IF (!( kontrola_email($hodnoty[1])) )     
    {
      echo $hodnoty[0].': <span class="chyba_staffer">Nevyhovující e-mail.adresa!</span>';
      $hodnoty[2] = 0;
    }
    ELSE {
      $hodnoty[1] = substr($hodnoty[1], 0, 50);
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
      
      $hodnoty[2] = 1;
      echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      echo '<input  name="'. $klic .'"  type="hidden" value="'.$hodnoty[1].'">'; //type="hidden"
    }
  break;
  default:
      $hodnoty[1] = trim ($hodnoty[1]);
      
      IF ( !(kontrola_email($hodnoty[1]))  )    
      {
        $hodnoty[2]=0;
      }
      ELSE {
        $hodnoty[2] = 1;
        $hodnoty[1] = substr($hodnoty[1], 0, 50);
        $hodnoty[1] = strip_tags ($hodnoty[1]);
        $hodnoty[1] = addslashes($hodnoty[1]);
      } 
  }
  return $hodnoty;
} // function email





function stav ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola true|false, [3] - název funkce
// $krok- určuje, co udělat:
//1-jen zobrazení bez kontroly,
//2- nastavi[2] 1, zobrazi hidden ,
//ostatní-nastavi[2] 1 
// Funkce vrací upravené pole $hodnoty (upraví [2]).
//
// Pole  je select , bez kontrol , neupravuje se.
// 
  Switch ($krok){
  case 1:
    echo $hodnoty[0].': ';   
    echo ' <select  size="1" name="'. $klic . '" class="vyber" >';
    echo ' <option '; if ($hodnoty[1]=="svobodný") echo 'selected' ; echo '>svobodný</option>';
    echo ' <option '; if ($hodnoty[1]=="ženatý/vdaná") echo 'selected' ; echo '>ženatý/vdaná</option>';
    echo ' </select> ' ;         
  break;
  case 2:
      $hodnoty[2] = 1;
      echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
     //type="hidden"
      echo ' <select  size="1" name="'. $klic . '"  type="hidden" class="vyber" >'; 
      echo ' <option '; if ($hodnoty[1]=="svobodný") echo 'selected' ; echo '>svobodný</option>';
      echo ' <option '; if ($hodnoty[1]=="ženatý/vdaná") echo 'selected' ; echo '>ženatý/vdaná</option>';
      echo ' </select> ' ;     
  break;
  default:
    
        $hodnoty[2] = 1;
  }
  return $hodnoty;
}//function stav




function vzdelani($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola true|false, [3] - název funkce
// $krok- určuje, co udělat:
//1-jen zobrazení bez kontroly,
//2- nastavi[2] 1, zobrazi hidden ,
//ostatní-nastavi[2] 1 
// Funkce vrací upravené pole $hodnoty (upraví [2]).
//
// Pole  je select , bez kontrol , neupravuje se.
// 
  Switch ($krok){
  case 1:
    echo $hodnoty[0].': ';   
    echo ' <select  size="1" name="'. $klic . '" class="vyber" >';
    echo ' <option '; if ($hodnoty[1]=="bez vzdělání") echo 'selected' ; echo '>bez vzdělání</option>';
    echo ' <option '; if ($hodnoty[1]=="základní") echo 'selected' ; echo '>základní</option>';
    echo ' <option '; if ($hodnoty[1]=="střední bez maturity") echo 'selected' ; echo '>střední bez maturity</option>';
    echo ' <option '; if ($hodnoty[1]=="střední s maturitou") echo 'selected' ; echo '>střední s maturitou</option>';
    echo ' <option '; if ($hodnoty[1]=="vysokoškolské") echo 'selected' ; echo '>vysokoškolské</option>';
    echo ' </select> ' ;         
  break;
  case 2:
      $hodnoty[2] = 1;
      echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      //type="hidden"
      echo ' <select  size="1" name="'. $klic . '"  type="hidden" class="vyber" >';
      echo ' <option '; if ($hodnoty[1]=="bez vzdělání") echo 'selected' ; echo '>bez vzdělání</option>';
      echo ' <option '; if ($hodnoty[1]=="základní") echo 'selected' ; echo '>základní</option>';
      echo ' <option '; if ($hodnoty[1]=="střední bez maturity") echo 'selected' ; echo '>střední bez maturity</option>';
      echo ' <option '; if ($hodnoty[1]=="střední s maturitou") echo 'selected' ; echo '>střední s maturitou</option>';
      echo ' <option '; if ($hodnoty[1]=="vysokoškolské") echo 'selected' ; echo '>vysokoškolské</option>';
      echo ' </select> ' ;     
  break;
  default:
    
        $hodnoty[2] = 1;
  }
  return $hodnoty;
}//function vzdelani



function ridicak ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola true|false, [3] - název funkce
// $krok- určuje, co udělat:
//1-jen zobrazení bez kontroly,
//2- nastavi[2] 1, zobrazi hidden ,
//ostatní-nastavi[2] 1 
// Funkce vrací upravené pole $hodnoty (upraví [2]).
//
// Pole  je select , bez kontrol , neupravuje se.
// 
  Switch ($krok){
  case 1:
    echo $hodnoty[0].': ';   
    echo ' <select  size="1" name="'. $klic . '" class="vyber" >';
    echo ' <option '; if ($hodnoty[1]=="A1") echo 'selected' ; echo '>A1</option>';
    echo ' <option '; if ($hodnoty[1]=="A") echo 'selected' ; echo '>A</option>';
    echo ' <option '; if ($hodnoty[1]=="B1") echo 'selected' ; echo '>B1</option>';
    echo ' <option '; if ($hodnoty[1]=="B") echo 'selected' ; echo '>B</option>';
    echo ' <option '; if ($hodnoty[1]=="C1") echo 'selected' ; echo '>C1</option>';
    echo ' <option '; if ($hodnoty[1]=="C") echo 'selected' ; echo '>C</option>';
    echo ' <option '; if ($hodnoty[1]=="D1") echo 'selected' ; echo '>D1</option>';
    echo ' <option '; if ($hodnoty[1]=="D") echo 'selected' ; echo '>D</option>';
    echo ' <option '; if ($hodnoty[1]=="BE") echo 'selected' ; echo '>BE</option>';
    echo ' <option '; if ($hodnoty[1]=="C1E") echo 'selected' ; echo '>C1E</option>';
    echo ' <option '; if ($hodnoty[1]=="CE") echo 'selected' ; echo '>CE</option>';
    echo ' <option '; if ($hodnoty[1]=="D1E") echo 'selected' ; echo '>D1E</option>';
    echo ' <option '; if ($hodnoty[1]=="DE") echo 'selected' ; echo '>DE</option>';
    echo ' <option '; if ($hodnoty[1]=="AM") echo 'selected' ; echo '>AM</option>';
    echo ' <option '; if ($hodnoty[1]=="T") echo 'selected' ; echo '>T</option>';
    echo ' </select> ' ;         
  break;
  case 2:
      $hodnoty[2] = 1;
      echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      //type="hidden"
      echo ' <select  size="1" name="'. $klic . '"  type="hidden" class="vyber" >';
      echo ' <option '; if ($hodnoty[1]=="A1") echo 'selected' ; echo '>A1</option>';
      echo ' <option '; if ($hodnoty[1]=="A") echo 'selected' ; echo '>A</option>';
      echo ' <option '; if ($hodnoty[1]=="B1") echo 'selected' ; echo '>B1</option>';
      echo ' <option '; if ($hodnoty[1]=="B") echo 'selected' ; echo '>B</option>';
      echo ' <option '; if ($hodnoty[1]=="C1") echo 'selected' ; echo '>C1</option>';
      echo ' <option '; if ($hodnoty[1]=="C") echo 'selected' ; echo '>C</option>';
      echo ' <option '; if ($hodnoty[1]=="D1") echo 'selected' ; echo '>D1</option>';
      echo ' <option '; if ($hodnoty[1]=="D") echo 'selected' ; echo '>D</option>';
      echo ' <option '; if ($hodnoty[1]=="BE") echo 'selected' ; echo '>BE</option>';
      echo ' <option '; if ($hodnoty[1]=="C1E") echo 'selected' ; echo '>C1E</option>';
      echo ' <option '; if ($hodnoty[1]=="CE") echo 'selected' ; echo '>CE</option>';
      echo ' <option '; if ($hodnoty[1]=="D1E") echo 'selected' ; echo '>D1E</option>';
      echo ' <option '; if ($hodnoty[1]=="DE") echo 'selected' ; echo '>DE</option>';
      echo ' <option '; if ($hodnoty[1]=="AM") echo 'selected' ; echo '>AM</option>';
      echo ' <option '; if ($hodnoty[1]=="T") echo 'selected' ; echo '>T</option>';
      echo ' </select> ' ;     
  break;
  default:
    
        $hodnoty[2] = 1;
  }
  return $hodnoty;
}//function ridicak




function  pc ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola true|false, [3] - název funkce
// $krok- určuje, co udělat:
//1-jen zobrazení bez kontroly,
//2- nastavi[2] 1, zobrazi hidden ,
//ostatní-nastavi[2] 1 
// Funkce vrací upravené pole $hodnoty (upraví [2]).
//
// Pole  je select , bez kontrol , neupravuje se.
// 
  Switch ($krok){
  case 1:
    echo $hodnoty[0].': ';   
    echo ' <select  size="1" name="'. $klic . '"   class="vyber" >';
    echo ' <option '; if ($hodnoty[1]=="žádná") echo 'selected' ; echo '>žádná</option>';
    echo ' <option '; if ($hodnoty[1]=="základy") echo 'selected' ; echo '>základy</option>';
    echo ' <option '; if ($hodnoty[1]=="kancelářské programy") echo 'selected' ; echo '>kancelářské programy</option>';
    echo ' <option '; if ($hodnoty[1]=="skladové systémy") echo 'selected' ; echo '>skladové systémy</option>';
    echo ' <option '; if ($hodnoty[1]=="CAD systémy") echo 'selected' ; echo '>CAD systémy</option>';
    echo ' <option '; if ($hodnoty[1]=="IT specialista") echo 'selected' ; echo '>IT specialista</option>';
    echo ' </select> ' ;         
  break;
  case 2:
      $hodnoty[2] = 1;
      echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      //type="hidden"
      echo ' <select  size="1" name="'. $klic . '"  type="hidden" class="vyber" >';
      echo ' <option '; if ($hodnoty[1]=="žádná") echo 'selected' ; echo '>žádná</option>';
      echo ' <option '; if ($hodnoty[1]=="základy") echo 'selected' ; echo '>základy</option>';
      echo ' <option '; if ($hodnoty[1]=="kancelářské programy") echo 'selected' ; echo '>kancelářské programy</option>';
      echo ' <option '; if ($hodnoty[1]=="skladové systémy") echo 'selected' ; echo '>skladové systémy</option>';
      echo ' <option '; if ($hodnoty[1]=="CAD systémy") echo 'selected' ; echo '>CAD systémy</option>';
      echo ' <option '; if ($hodnoty[1]=="IT expert") echo 'selected' ; echo '>IT expert</option>';
      echo ' </select> ' ;     
  break;
  default:
    
        $hodnoty[2] = 1;
  }
  return $hodnoty;
}//function pc




function   jazyk ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola true|false, [3] - název funkce
// $krok- určuje, co udělat:
//1-jen zobrazení bez kontroly,
//2- nastavi[2] 1, zobrazi hidden ,
//ostatní-nastavi[2] 1 
// Funkce vrací upravené pole $hodnoty (upraví [2]).
//
// Pole  je select , bez kontrol , neupravuje se.
// 
  Switch ($krok){
  case 1:
    echo $hodnoty[0].': ';   
    echo ' <select  size="1" name="'. $klic . '" class="vyber" >';
    echo ' <option '; if ($hodnoty[1]=="žádný") echo 'selected' ; echo '>žádný</option>';
    echo ' <option '; if ($hodnoty[1]=="anglický") echo 'selected' ; echo '>anglický</option>';
    echo ' <option '; if ($hodnoty[1]=="francouzský") echo 'selected' ; echo '>francouzský</option>';
    echo ' </select> ' ;         
  break;
  case 2:
      $hodnoty[2] = 1;
      echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      //type="hidden"
      echo ' <select  size="1" name="'. $klic . '"  type="hidden" class="vyber" >';
      echo ' <option '; if ($hodnoty[1]=="žádný") echo 'selected' ; echo '>žádný</option>';
      echo ' <option '; if ($hodnoty[1]=="anglický") echo 'selected' ; echo '>anglický</option>';
      echo ' <option '; if ($hodnoty[1]=="francouzský") echo 'selected' ; echo '>francouzský</option>';
      echo ' </select> ' ;     
  break;
  default:
    
        $hodnoty[2] = 1;
  }
  return $hodnoty;
}//function  jazyk




function   jazyk_uroven ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola true|false, [3] - název funkce
// $krok- určuje, co udělat:
//1-jen zobrazení bez kontroly,
//2- nastavi[2] 1, zobrazi hidden ,
//ostatní-nastavi[2] 1 
// Funkce vrací upravené pole $hodnoty (upraví [2]).
//
// Pole  je select , bez kontrol , neupravuje se.
// 
  Switch ($krok){
  case 1:
    echo $hodnoty[0].': ';   
    echo ' <select  size="1" name="'. $klic . '" class="vyber" >';
    echo ' <option '; if ($hodnoty[1]=="žádná") echo 'selected' ; echo '>žádná</option>';
    echo ' <option '; if ($hodnoty[1]=="začátečník") echo 'selected' ; echo '>začátečník</option>';
    echo ' <option '; if ($hodnoty[1]=="pokročilý") echo 'selected' ; echo '>pokročilý</option>';
    echo ' <option '; if ($hodnoty[1]=="rodilý mluvčí") echo 'selected' ; echo '>rodilý mluvčí</option>';
    echo ' </select> ' ;         
  break;
  case 2:
      $hodnoty[2] = 1;
      echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      //type="hidden"
      echo ' <select  size="1" name="'. $klic . '"  type="hidden" class="vyber" >';
      echo ' <option '; if ($hodnoty[1]=="žádná") echo 'selected' ; echo '>žádná</option>';
      echo ' <option '; if ($hodnoty[1]=="začátečník") echo 'selected' ; echo '>začátečník</option>';
      echo ' <option '; if ($hodnoty[1]=="pokročilý") echo 'selected' ; echo '>pokročilý</option>';
      echo ' <option '; if ($hodnoty[1]=="rodilý mluvčí") echo 'selected' ; echo '>rodilý mluvčí</option>';
      echo ' </select> ' ;     
  break;
  default:
    
        $hodnoty[2] = 1;
  }
  return $hodnoty;
}//function  jazyk_uroven


function   kurz ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola true|false, [3] - název funkce
// $krok- určuje, co udělat:
//1-jen zobrazení bez kontroly,
//2- nastavi[2] 1, zobrazi hidden ,
//ostatní-nastavi[2] 1 
// Funkce vrací upravené pole $hodnoty (upraví [2]).
//
// Pole  je select , bez kontrol , neupravuje se.
// 
  Switch ($krok){
  case 1:
    echo $hodnoty[0] . ': <input type="checkbox" name="' . $klic . '" class="box" value="1" ';   //
    if ( $hodnoty[1] == '1' ) {echo 'checked >';}                     
      
  break;
  case 2:
      $hodnoty[2] = 1;
      
      echo $hodnoty[0].': <span class="hodnota_staffer">' . '</span>';
      //type="hidden"
      echo '<input type="checkbox" name="' . $klic . '" type="hidden"  class="box" value="1" ';   //
      if ( $hodnoty[1] == '1' ) {echo 'checked >';}  
       
  break;
  default:
    
        $hodnoty[2] = 1;
  }
  return $hodnoty;
}//function kurz 



?>