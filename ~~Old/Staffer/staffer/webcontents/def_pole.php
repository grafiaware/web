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
        $d = mb_substr($dat,0,10, "UTF-8"); 
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
       $d = mb_substr($dat,0,4, "UTF-8"); 
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
       $t = mb_substr($text,0,50, "UTF-8");
        
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
       $t = mb_substr($text,0,5, "UTF-8");
        
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
       $t = mb_substr($text,0,10,"UTF-8");
        
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
       $t = mb_substr($text,0,40,"UTF-8");
        
       if (!(ereg("^[0-9\.\+\/\-]{9,}$" , $t) )) {$OK=FALSE; }
       else  {$OK=TRUE;}
   
       return $OK ;

  } // function kontrola_telefon
  
  
//-----------------------------------------------------------------------------  
  

function text10_ ($klic,$hodnoty,$krok,$clas='' ) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola chyba|ok, [3] - název funkce
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
    if ($clas)  { echo "<span class=".$clas .">";}
    echo $hodnoty[0]. ":";
    if ($clas)  { echo "</span>";}
    echo '<input type="text" name="'.$klic.'" value="'.$hodnoty[1].
                         '" class="staffer_vstupni_text" maxlength="10" size="4">';   //style="width:70px
  break;
  case 2:
    $hodnoty[1] = trim ($hodnoty[1]);
    
      $hodnoty[1] = mb_substr($hodnoty[1], 0, 10, "UTF-8");
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
      
      $hodnoty[2] = 1;
      if ($clas)  { echo "<span class=".$clas .">";}
      echo $hodnoty[0]. ":";
      if ($clas)  { echo "</span>";}
      echo '<span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      echo '<input  name="'. $klic .'" type="hidden"  value="'.$hodnoty[1].'">';   //type="hidden"
    
  break;
  default:
    $hodnoty[1] = trim ($hodnoty[1]);
      
      $hodnoty[2] = 1;
      $hodnoty[1] = mb_substr($hodnoty[1], 0, 50, "UTF-8");
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
       
  }
  return $hodnoty;
} //function text10_




function text50 ($klic,$hodnoty,$krok,$clas='') {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola chyba|ok, [3] - název funkce
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
    if ($clas)  { echo "<span class=".$clas .">";}  
    echo $hodnoty[0] . ":)* ";
    if ($clas)  { echo "</span>";}  
    echo '&nbsp;<input type="text" name="'.$klic.'" value="'.$hodnoty[1].
                         '" class="staffer_vstupni_text" maxlength="50" size="15">';
  break;
  case 2:
    $hodnoty[1] = trim ($hodnoty[1]);
    
    IF (mb_strlen($hodnoty[1],"UTF-8")<2)
    {
      if ($clas)  { echo "<span class=".$clas .">";}   
      echo $hodnoty[0]. ":)* ";
      if ($clas)  { echo "</span>";}  
      echo '<span class="chyba_staffer">Nevyplněné povinné pole!</span>';
      $hodnoty[2] = 0;
    }
    ELSE {
      $hodnoty[1] = mb_substr($hodnoty[1], 0, 50, "UTF-8");
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
      $hodnoty[2] = 1;
      
      if ($clas)  { echo "<span class=".$clas .">";} 
      echo $hodnoty[0] . ":)* ";
      if ($clas)  { echo "</span>";}  
      echo '<span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      echo '<input  name="'. $klic .'"  type="hidden"  value="'.$hodnoty[1].'">'; //type="hidden"
    }
  break;
  default:
      $hodnoty[1] = trim ($hodnoty[1]); 
      IF (mb_strlen($hodnoty[1],"UTF-8")<2)
      {
        $hodnoty[2]=0;
      }
      ELSE {
        $hodnoty[2] = 1;
        $hodnoty[1] = mb_substr($hodnoty[1], 0, 50, "UTF-8");
        $hodnoty[1] = strip_tags ($hodnoty[1]);
        $hodnoty[1] = addslashes($hodnoty[1]);
      } 
  }
  return $hodnoty;
} //function text50


function text100 ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola chyba|ok, [3] - název funkce
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
                        '" class="staffer_vstupni_text" maxlength="100">';
  break;
  case 2:
    $hodnoty[1] = trim ($hodnoty[1]);
    
    IF (mb_strlen($hodnoty[1],"UTF-8")<2)
    {
      echo $hodnoty[0].': <span class="chyba_staffer">Nevyplněné povinné pole!</span>';
      $hodnoty[2] = 0;
    }
    ELSE {
      $hodnoty[1] = mb_substr($hodnoty[1], 0, 100, "UTF-8");
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
      $hodnoty[2] = 1;
      echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      echo '<input  name="'. $klic .'"  type="hidden" value="'.$hodnoty[1].'">'; //type="hidden"
    }
  break;
  default:
      $hodnoty[1] = trim ($hodnoty[1]); 
      IF (mb_strlen($hodnoty[1], "UTF-8")<2)
      {
        $hodnoty[2]=0;
      }
      ELSE {
        $hodnoty[2] = 1;
        $hodnoty[1] = mb_substr($hodnoty[1], 0, 100, "UTF-8");
        $hodnoty[1] = strip_tags ($hodnoty[1]);
        $hodnoty[1] = addslashes($hodnoty[1]);
      } 
  }
  return $hodnoty;
}//function text100




function text255_ ($klic,$hodnoty,$krok,$clas='') {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola chyba|ok, [3] - název funkce
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
    if ($clas)  { echo "<span class=".$clas .">";}  
    echo $hodnoty[0].': ';
    if ($clas)  { echo "</span>";}
    echo'<input type="text" name="'.$klic.'" value="'.$hodnoty[1].
                         '" class="staffer_vstupni_text" maxlength="255">';
  break;
  case 2:
    $hodnoty[1] = trim ($hodnoty[1]);
    
      $hodnoty[1] = mb_substr($hodnoty[1], 0, 255, "UTF-8");
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
      
      $hodnoty[2] = 1;
      if ($clas)  { echo "<span class=".$clas .">";}
      echo $hodnoty[0].': ';
      if ($clas)  { echo "</span>";}
      echo  '<span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      echo '<input  name="'. $klic .'" type="hidden"  value="'.$hodnoty[1].'">'; //type="hidden"
    
  break;
  default:
    $hodnoty[1] = trim ($hodnoty[1]);
      
      $hodnoty[2] = 1;
      $hodnoty[1] = mb_substr($hodnoty[1], 0, 255, "UTF-8");
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
       
  }
  return $hodnoty;
} //function text255_





function poznamka_ ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola chyba|ok, [3] - název funkce
// $krok- určuje, co udělat:
//1-jen zobrazení bez kontroly,
//2-trim, kontrola, pri OK - zkrati,upravi [1], nastavi[2] 1, zobrazi hidden upravené hodnoty,
//                    pri neOK - nastavi [2] 0, zobrazi chybu (zde nenastava, povoleno vse)
//ostatní-trim, kontrola, nastavi[2] popr[1] jako u kroku 2
// Funkce vrací upravené pole $hodnoty (upraví [1],[2]).
//
// Pole  je text, nepovinné, ořízne na délku 800
//       
  Switch ($krok){
  case 1:
    echo $hodnoty[0].':<br><textarea cols="60" rows="10" class="txtpole"  name="' . $klic .
          '">' . $hodnoty[1] .  '</textarea><br>' ;
  break;
  case 2:
    $hodnoty[1] = trim ($hodnoty[1]);
    
      $hodnoty[1] = mb_substr($hodnoty[1], 0, 800, "UTF-8");
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
      
      $hodnoty[2] = 1;
      echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span><br>';
      //type="hidden"
      echo '<input  name="'. $klic .'" type="hidden"  value="'.$hodnoty[1].'">';     
    
  break;
  default:
      $hodnoty[1] = trim ($hodnoty[1]);
      
        $hodnoty[2] = 1;
        $hodnoty[1] = mb_substr($hodnoty[1], 0, 800, "UTF-8");
        $hodnoty[1] = strip_tags ($hodnoty[1]);
        $hodnoty[1] = addslashes($hodnoty[1]);
      } 
  
  return $hodnoty;
} //function poznamka_



function dotaz ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola chyba|ok, [3] - název funkce
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
    echo $hodnoty[0].':)*<br><textarea cols="60" rows="10" class="txtpole"  name="' . $klic .
          '">' . $hodnoty[1] .  '</textarea><br>' ;
  break;
  case 2:
    $hodnoty[1] = trim ($hodnoty[1]);
    
      $hodnoty[1] = mb_substr($hodnoty[1], 0, 800, "UTF-8");
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
      
      //echo "<br>*" . trim($hodnoty[1]) . "*";
      if (trim($hodnoty[1])) {
          $hodnoty[2] = 1;
          echo $hodnoty[0].':)* <span class="hodnota_staffer">'.$hodnoty[1].'</span><br>';
      }
      else {
          $hodnoty[2] = 0;
          echo $hodnoty[0].':)* <span class="chyba_staffer">Nevyplněné povinné pole!</span>';
      }
      
      //$hodnoty[2] = 1;
      //echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span><br>';
      //
      //type="hidden"
      echo '<input  name="'. $klic .'" type="hidden"  value="'.$hodnoty[1].'">';     
    
  break;
  default:
      $hodnoty[1] = trim ($hodnoty[1]);
      
      
        $hodnoty[2] = 1;
        $hodnoty[1] = mb_substr($hodnoty[1], 0, 800, "UTF-8");
        $hodnoty[1] = strip_tags ($hodnoty[1]);
        $hodnoty[1] = addslashes($hodnoty[1]);
        
        if (trim($hodnoty[1])) {
          $hodnoty[2] = 1;
        }
        else {
          $hodnoty[2] = 0;
        }
        
      } 
  
  return $hodnoty;
} //function dotaz






function narozen_ ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola chyba|ok, [3] - název funkce
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
                         '" class="staffer_vstupni_text" maxlength="10">';
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
      $hodnoty[1] = mb_substr($hodnoty[1], 0, 10, "UTF-8");
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
        $hodnoty[1] = mb_substr($hodnoty[1], 0, 10, "UTF-8");
        $hodnoty[1] = strip_tags ($hodnoty[1]);
        $hodnoty[1] = addslashes($hodnoty[1]);
      } 
  }
  return $hodnoty;
} //function narozen_




function datum_ ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola chyba|ok, [3] - název funkce
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
                         '" class="staffer_vstupni_text" maxlength="10">';
  break;
  case 2:
    $hodnoty[1] = trim ($hodnoty[1]);
    
    if (mb_strlen( $hodnoty[1], "UTF-8") < 5) {
        $O = kontrola_datum_rok($hodnoty[1]);
    }
    else{
        $O = kontrola_datum($hodnoty[1]);
    }
    if ($hodnoty[1]=="") {$O = true;}
    
    IF ( !( $O ) )      {
      echo $hodnoty[0].': <span class="chyba_staffer">Nevyhovující datum!</span>';
      $hodnoty[2] = 0;
    }
    ELSE {
      if (mb_strlen( $hodnoty[1], "UTF-8") < 8){
            $hodnoty[1] = mb_substr($hodnoty[1], 0, 4, "UTF-8");
      }
      else{
            $hodnoty[1] = mb_substr($hodnoty[1], 0, 10, "UTF-8");
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
      
      if (mb_strlen( $hodnoty[1], "UTF-8") < 5) {
        $O = kontrola_datum_rok($hodnoty[1]); }
      else{
        $O = kontrola_datum($hodnoty[1]); }
      if ($hodnoty[1]=="") {$O = true;}  
      
      IF ( !($O) )      {
        $hodnoty[2]=0;
      }
      ELSE {
        $hodnoty[2] = 1;
        if (mb_strlen( $hodnoty[1], "UTF-8") < 5) {
            $hodnoty[1] = mb_substr($hodnoty[1], 0, 4, "UTF-8");
        }
        else {
            $hodnoty[1] = mb_substr($hodnoty[1], 0, 10,"UTF-8");
        }  
        $hodnoty[1] = strip_tags ($hodnoty[1]);
        $hodnoty[1] = addslashes($hodnoty[1]);
      } 
  }
  return $hodnoty;
} //function datum_




function cislo_domu ($klic,$hodnoty,$krok , $clas='') {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola chyba|ok, [3] - název funkce
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
    if ($clas)  { echo "<span class=".$clas .">";}  
    echo $hodnoty[0].':)* ';
    if ($clas)  { echo "</span>";}  
    echo  '<input type="text" name="'.$klic.'" value="'.$hodnoty[1].
                         '" class="staffer_vstupni_text" maxlength="10" size="4">';  //style="width: 60px">';
  break;
  case 2:
    $hodnoty[1] = trim ($hodnoty[1]);
    
    IF (!( kontrola_cislo_domu($hodnoty[1])) )     
    {
        if ($clas)  { echo "<span class=".$clas .">";}
        echo $hodnoty[0].':)* '; 
        if ($clas)  { echo "</span>";}  
      echo '<span class="chyba_staffer">Nevyhovující číslo domu!</span>';
      $hodnoty[2] = 0;
    }
    ELSE {
      $hodnoty[1] = mb_substr($hodnoty[1], 0, 10, "UTF-8");
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
      
      $hodnoty[2] = 1;
      if ($clas)  { echo "<span class=".$clas .">";}
      echo $hodnoty[0].':)* ';
      if ($clas)  { echo "</span>";}  
      echo '<span class="hodnota_staffer">'.$hodnoty[1].'</span>';
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
        $hodnoty[1] = mb_substr($hodnoty[1], 0, 10, "UTF-8");
        $hodnoty[1] = strip_tags ($hodnoty[1]);
        $hodnoty[1] = addslashes($hodnoty[1]);
      } 
  }
  return $hodnoty;
} // function cislo_domu





function psc ($klic,$hodnoty,$krok, $clas='') {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola chyba|ok, [3] - název funkce
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
    if ($clas)  { echo "<span class=".$clas .">";}  
    echo $hodnoty[0].':)* ';
    if ($clas)  { echo "</span>";}    
    echo '<input type="text" name="'.$klic.'" value="'.$hodnoty[1].
                         '" class="staffer_vstupni_text" maxlength="5" size="4">'; //style="width:40px">';
  break;
  case 2:
    $hodnoty[1] = trim ($hodnoty[1]);
    
    IF (!( kontrola_psc($hodnoty[1])) )     
    {
      if ($clas)  { echo "<span class=".$clas .">";}  
      echo $hodnoty[0].':)* ';
      if ($clas)  { echo "</span>";}  
      echo '<span class="chyba_staffer">Nevyhovující PSČ!</span>';
      $hodnoty[2] = 0;
    }
    ELSE {
      $hodnoty[1] = mb_substr($hodnoty[1], 0, 5, "UTF-8");
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
      
      $hodnoty[2] = 1;
      
      if ($clas)  { echo "<span class=".$clas .">";}
      echo $hodnoty[0].':)* ';
      if ($clas)  { echo "</span>";}  
      echo ' <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
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
        $hodnoty[1] = mb_substr($hodnoty[1], 0, 5, "UTF-8");
        $hodnoty[1] = strip_tags ($hodnoty[1]);
        $hodnoty[1] = addslashes($hodnoty[1]);
      } 
  }
  return $hodnoty;
} // function psc




function telefon ($klic,$hodnoty,$krok,$clas='') {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola chyba|ok, [3] - název funkce
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
    if ($clas)  { echo "<span class=".$clas .">";}  
    echo $hodnoty[0].':)* ';
    if ($clas)  { echo "</span>";}
    echo '<input type="text" name="'.$klic.'" value="'.$hodnoty[1].
                         '" class="staffer_vstupni_text" maxlength="40">';
  break;
  case 2:
    $hodnoty[1] = trim ($hodnoty[1]);
    
    IF (!( kontrola_telefon($hodnoty[1])) )     
    {
      if ($clas)  { echo "<span class=".$clas .">";}  
      echo $hodnoty[0].':)* '; 
      if ($clas)  { echo "</span>";}
      echo '<span class="chyba_staffer">Nevyhovující číslo telefonu!</span>';
      $hodnoty[2] = 0;
    }
    ELSE {
      $hodnoty[1] = mb_substr($hodnoty[1], 0, 40, "UTF-8");
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
      
      $hodnoty[2] = 1;
      if ($clas)  { echo "<span class=".$clas .">";}
      echo $hodnoty[0].':)* '; 
      if ($clas)  { echo "</span>";}
      echo '<span class="hodnota_staffer">'.$hodnoty[1].'</span>';
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
        $hodnoty[1] = mb_substr($hodnoty[1], 0, 40, "UTF-8");
        $hodnoty[1] = strip_tags ($hodnoty[1]);
        $hodnoty[1] = addslashes($hodnoty[1]);
      } 
  }
  return $hodnoty;
} // function telefon





function email ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola chyba|ok, [3] - název funkce
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
    echo $hodnoty[0].':)* <input type="text" name="'.$klic.'" value="'.$hodnoty[1].
                         '" class="staffer_vstupni_text" maxlength="50">';
  break;
  case 2:
    $hodnoty[1] = trim ($hodnoty[1]);
    
    IF (!( kontrola_email($hodnoty[1])) )     
    {
      echo $hodnoty[0].':)* <span class="chyba_staffer">Nevyhovující e-mail.adresa!</span>';
      $hodnoty[2] = 0;
    }
    ELSE {
      $hodnoty[1] = mb_substr($hodnoty[1], 0, 50, "UTF-8");
      $hodnoty[1] = strip_tags ($hodnoty[1]);
      $hodnoty[1] = addslashes($hodnoty[1]);
      
      $hodnoty[2] = 1;
      echo $hodnoty[0].':)* <span class="hodnota_staffer"><a href="mailto:'.$hodnoty[1].'">'.$hodnoty[1].'</a></span>';
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
        $hodnoty[1] = mb_substr($hodnoty[1], 0, 50, "UTF-8");
        $hodnoty[1] = strip_tags ($hodnoty[1]);
        $hodnoty[1] = addslashes($hodnoty[1]);
      } 
  }
  return $hodnoty;
} // function email





function stav ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola chyba|ok, [3] - název funkce
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
    echo ' <option '; if ($hodnoty[1]=="svobodný/á") echo 'selected' ; echo '>svobodný/á</option>';
    echo ' <option '; if ($hodnoty[1]=="ženatý/vdaná") echo 'selected' ; echo '>ženatý/vdaná</option>';
    echo ' <option '; if ($hodnoty[1]=="rozvedený/á") echo 'selected' ; echo '>rozvedený/á</option>';
    echo ' <option '; if ($hodnoty[1]=="ovdovělý/á") echo 'selected' ; echo '>ovdovělý/á</option>';
    echo ' </select> ' ;         
  break;
  case 2:
      $hodnoty[2] = 1;
      echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
     //type="hidden"
     echo '<input  name="'. $klic .'" type="hidden"  value="'.$hodnoty[1].'">';  
  break;
  default:
    
        $hodnoty[2] = 1;
  }
  return $hodnoty;
}//function stav




function vzdelani($klic,$hodnoty,$krok,$clas='') {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola chyba|ok, [3] - název funkce
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
    if ($clas)  { echo "<span class=".$clas .">";}  
    echo $hodnoty[0].': ';   
    if ($clas)  { echo "</span>";}
    echo '<select  size="1" name="'. $klic . '" class="vyber" >';
    echo ' <option '; if ($hodnoty[1]=="bez vzdělání") echo 'selected' ; echo '>bez vzdělání</option>';
    echo ' <option '; if ($hodnoty[1]=="základní") echo 'selected' ; echo '>základní</option>';
    echo ' <option '; if ($hodnoty[1]=="střední bez maturity") echo 'selected' ; echo '>střední bez maturity</option>';
    echo ' <option '; if ($hodnoty[1]=="střední s maturitou") echo 'selected' ; echo '>střední s maturitou</option>';
    echo ' <option '; if ($hodnoty[1]=="vysokoškolské") echo 'selected' ; echo '>vysokoškolské</option>';
    echo ' </select> ' ;         
  break;
  case 2:
      $hodnoty[2] = 1;
      if ($clas)  { echo "<span class=".$clas .">";}
      echo $hodnoty[0].':' ;
      if ($clas)  { echo "</span>";}
      echo  '<span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      //type="hidden"
      echo '<input  name="'. $klic .'" type="hidden"  value="'.$hodnoty[1].'">';  
  break;
  default:
    
        $hodnoty[2] = 1;
  }
  return $hodnoty;
}//function vzdelani



function ridicak ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola chyba|ok, [3] - název funkce
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
    echo ' <option '; if ($hodnoty[1]=="nemám") echo 'selected' ; echo '>nemám</option>';
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
      echo '<input  name="'. $klic .'" type="hidden"  value="'.$hodnoty[1].'">';
  break;
  default:
    
        $hodnoty[2] = 1;
  }
  return $hodnoty;
}//function ridicak




function  pc ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola chyba|ok, [3] - název funkce
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
    echo ' <option '; if ($hodnoty[1]=="systémy pro ekonomiku a řízení výroby") echo 'selected' ; echo '>systémy pro ekonomiku a řízení výroby</option>';
    echo ' <option '; if ($hodnoty[1]=="IT specialista") echo 'selected' ; echo '>IT specialista</option>';
    echo ' </select> ' ;         
  break;
  case 2:
      $hodnoty[2] = 1;
      echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      //type="hidden"
      echo '<input  name="'. $klic .'" type="hidden"  value="'.$hodnoty[1].'">';   
  break;
  default:
    
        $hodnoty[2] = 1;
  }
  return $hodnoty;
}//function pc




function   jazyk ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola chyba|ok, [3] - název funkce
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
    echo ' <select  size="1" name="'. $klic .'" class="vyber" >';
    echo ' <option '; if ($hodnoty[1]=="žádné") echo 'selected' ; echo '>žádné</option>';
    echo ' <option '; if ($hodnoty[1]=="anglický") echo 'selected' ; echo '>anglický jazyk</option>';
    echo ' <option '; if ($hodnoty[1]=="německý") echo 'selected' ; echo '>německý jazyk</option>';
    echo ' <option '; if ($hodnoty[1]=="francouzský") echo 'selected' ; echo '>francouzský jazyk</option>';
    echo ' <option '; if ($hodnoty[1]=="jiný") echo 'selected' ; echo '>jiný jazyk</option>';
    echo ' </select> ' ;         
  break;
  case 2:
      $hodnoty[2] = 1;
      echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      //type="hidden"
      echo '<input  name="'. $klic .'" type="hidden"  value="'.$hodnoty[1].'">';   //type="hidden"
          
  break;
  default:
    
        $hodnoty[2] = 1;
  }
  return $hodnoty;
}//function  jazyk




function   jazyk_uroven ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola chyba|ok, [3] - název funkce
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
      echo '<input  name="'. $klic .'" type="hidden"  value="'.$hodnoty[1].'">';   
  break;
  default:
    
        $hodnoty[2] = 1;
  }
  return $hodnoty;
}//function  jazyk_uroven



function   kurz ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola chyba|ok, [3] - název funkce
// $krok- určuje, co udělat:
//1-jen zobrazení bez kontroly,
//2- nastavi[2] 1, zobrazi hidden ,
//ostatní-nastavi[2] 1 
// Funkce vrací upravené pole $hodnoty (upraví [2]).
// 
  Switch ($krok){
  case 1:
    echo '<input type="checkbox" name="' . $klic . '" class="box" value="ANO" ';   //
    if ( $hodnoty[1] == 'ANO' ) {echo 'checked > ';} else {echo '> ';}  
    echo $hodnoty[0];                  
      
  break;
  case 2:
      $hodnoty[2] = 1;
      if ($hodnoty[1]=='') {$hodnoty[1]='NE';}
      echo $hodnoty[0].': <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      //type="hidden"
      echo '<input  name="'. $klic .'"  type="hidden" value="'.$hodnoty[1].'">'; 
      
      //echo $hodnoty[0].': <span class="hodnota_staffer">' . '</span>';
      ////type="hidden"
      //echo '<input type="checkbox" name="' . $klic . '" type="hidden"  class="box" value="1" ';   //
      //if ( $hodnoty[1] == '1' ) {echo 'checked >';}  
       
  break;
  default:
    
        $hodnoty[2] = 1;
  }
  return $hodnoty;
}//function kurz 




function   checkBpovinny ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola chyba|ok, [3] - název funkce
// $krok- určuje, co udělat:
//1-jen zobrazení bez kontroly,
//2- nastavi[2] 1, zobrazi hidden ,
//ostatní-nastavi[2] 1 
// Funkce vrací upravené pole $hodnoty (upraví [2]).
// 
  Switch ($krok){
  case 1:
    echo $hodnoty[0]. ":)* ";   
    echo '<input type="checkbox" name="' . $klic . '" class="box" value="ANO" ';   //
    if ( $hodnoty[1] == 'ANO' ) {echo 'checked > ';} else {echo '> ';}  
                    
  break;
  
  case 2:
//    IF (!( $hodnoty[1]) or( $hodnoty[1]=='NE') )     
  
    IF ( $hodnoty[1]=='ANO') {
      $hodnoty[2] = 1;
      
      echo $hodnoty[0].' :)* <span class="hodnota_staffer">'.$hodnoty[1].'</span>';
      echo '<input  name="'. $klic .'"  type="hidden" value="'.$hodnoty[1].'">'; //type="hidden"
    } 
    ELSE {
      echo $hodnoty[0].':)* <span class="chyba_staffer">Nutno zaškrtnout!</span>';
      $hodnoty[2] = 0;
    }
       
  break;
  default:
      IF ( $hodnoty[1]=='ANO') {
        $hodnoty[2] = 1;
      }
     ELSE {
        $hodnoty[2] = 0;
     }   
  }
  return $hodnoty;
}//function checkBpovinny



function zivotopis ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola chyba|ok, [3] - název funkce
// $krok- určuje, co udělat.
// Stahne a zkontroluje soubor
//         
Switch ($krok){
case 1:
  
    if (isset ($_POST['zivotopis'])) {
                                      echo '<input type="hidden" value="'.$_POST['zivotopis'].'" name="zivotopis">';
                                      echo 'Váš životopis byl již vpořádku zpracován.';
                                      $hodnoty[2] = 1;
                                     } 
    else {echo '<br>' .$hodnoty[0].':)* <br><input type="file" name="fiadresa" size="85" class="staffer_input_drobny"> <br><br>';}   //style

  break;

case 2:
    //Verejna cast////////////////////////////////////////////////////////
  if (!isset ($_GET['odpoved'])) { //odliseni verejne casti od rs
        if (isset ($_POST['zivotopis'])) { //soubor byl uz zadan, bylo pouzito zpet
          echo '<input type="hidden" name="zivotopis" value="'.$_POST['zivotopis'].'">';
          echo 'Soubor s Vaším životopisem byl již v pořádku uložen.';
        } 
        else { 

          if ($_FILES['fiadresa']['name']!='') { //kontrola zda byl zadan soubor ke stazeni
            if ($_FILES['fiadresa']['size']<=2000000 OR $_FILES['fiadresa']['size'] == 0) { //kontrola velikosti
                //ziskani pripony
                $name = $_FILES['fiadresa']['name'];
                $typ = $_FILES['fiadresa']['type'];
                $pripona = trim($name);
                $ok = 0;
                $tecka = mb_substr($pripona,-5,-4, "UTF-8");
                if ($tecka == '.') {$pripona = mb_substr($pripona,-4,4,"UTF-8"); $ok=1;}
                else {$tecka = mb_substr($pripona,-4,-3, "UTF-8");
                    if ($tecka == '.') {$pripona = mb_substr($pripona,-3,3,"UTF-8"); $ok=1;}
                }
                ///////////////// 
                if (!$ok) {//nelze zjistit priponu
                    echo '<span class="chyba_staffer">Neznámý typ souboru, nelze přijmout!</span>';
                    $hodnoty[2] = 0;
                }
                else {
                    $pripona = mb_strtolower ($pripona,"UTF-8") ;
                //jen nasledujici typy pripon
                    if ($pripona=='txt'  OR  
                        $pripona=='xls'  OR  $pripona=='doc'  OR  $pripona=='xlsx'  OR   $pripona=='docx' OR
                        $pripona=='pdf'  OR  $pripona=='rtf'  OR  $pripona=='odt'   OR   $pripona=='ods'  OR
                        $pripona=='ppt')
                    {
                        $id = uniqid(rand(), 0);
                        $nazev=$id.'.'.$pripona;
                        MySQL_Query("INSERT INTO staffer_zivotopisy (id,nazev,pripona,typ) VALUES ('$id','$nazev','$pripona','$typ')");
                        //MySQL_Query("INSERT INTO staffer_zivotopisy (id,nazev,pripona,typ) VALUES ('$id','$name','$pripona','$typ')");
                        $umisteni = 'rs/app/staffer/files/'.$id.'.'.$pripona;
                        //$umisteni = 'rs/app/staffer/files/'.$name;
                        $ok = move_uploaded_file($_FILES['fiadresa']['tmp_name'],$umisteni); chmod ($umisteni, 0777); 
                        if ($ok) {
                                echo '<input type="hidden" name="zivotopis" value="'.$id.'">';
                                echo '<span class="hodnota_staffer">'.$name.'</span>';
                        }
                        else {
                            echo '<span class="chyba_staffer">Soubor '.$name.' nebyl uložen!</span>';
                        }
                        
                        $hodnoty[1] = $id;

                    }
                }


                //move_uploaded_file($_FILES['fiadresa']['tmp_name'],$umisteni);
                //chmod ($umisteni, 0777);
                $hodnoty[2] = 1;
            }
            else {
            //hlaseni o prekrocene velikosti
               echo '<span class="chyba_staffer">Soubor s životopisem je příliš velký! Maximální velikost souboru je 1600kB.</span>';
               $hodnoty[2] = 0;
            }
          }
        else {
        //hlasni o nevlozeni zivotopisu
                $hodnoty[2] = 0;
                echo $hodnoty[0].': <span class="chyba_staffer">Nevložili jste soubor s životopisem!</span>';
        }
        }
  } 
    /////////////////////////////////////////////////////////////////////// 
  else
    //Redakcni cast////////////////////////////////////////////////////////
  {
     if (isset($_GET['odpoved'])) {
                                   $id = $hodnoty[1];
                                   $vysledek = MySQL_Query("SELECT nazev FROM staffer_zivotopisy WHERE id='$id' LIMIT 1");
                                   $zaznam = MySQL_Fetch_Array($vysledek);
                                   
                                   $cesta = 'app/staffer/files/'.$zaznam['nazev'];
                                   if ($zaznam['nazev']) {
                                     echo 'Soubor se životopisem:)* <br>';
                                     echo '<a href="'.$cesta.'" target="_blank">STÁHNOUT</a>';
                                   }  
                                  }
  }
    ///////////////////////////////////////////////////////////////////////                      
  break;
  
default:
        if (isset ($_POST[zivotopis])) {
            $hodnoty[2] = 1;
        }
        else {
                if ($krok != 3) {
                    $hodnoty[2] = 0;
                    if ($_FILES['fiadresa']['name']!='') {
                      if ($_FILES['fiadresa']['size']<=2000000) {
                        $hodnoty[2] = 1;
                      }
                      else {;}
                    }
                    else {
                         $hodnoty[2] = 0;
                    }
                }
                else {
                    $hodnoty[2] = 1;
                }
       }
} 
  return $hodnoty;
} //function zivotopis



function zivotopis_nepovinny ($klic,$hodnoty,$krok) {
// $klic -  jméno(atrib.name) prvku ,
// $hodnoty - [0] - popisek ve formuláří, [1]  - obsah(hodnota) prvku(pole), [2] - 0|1 kontrola chyba|ok, [3] - název funkce
// $krok- určuje, co udělat.
// Stahne a zkontroluje soubor
//         
Switch ($krok){
case 1:
  
    if (isset ($_POST['zivotopis_nepovinny'])) {
                                      echo '<input type="hidden" value="'.$_POST['zivotopis_nepovinny'].'" name="zivotopis_nepovinny">';
                                      echo 'Váš životopis byl již vpořádku zpracován.';
                                      $hodnoty[2] = 1;
                                     } 
    else {echo '<br>' . $hodnoty[0].': <br><input type="file" name="fiadresa" size="85" class="staffer_input_drobny"><br><br>';}  //style

   
  break;
case 2:
    //Verejna cast////////////////////////////////////////////////////////
    if (!isset ($_GET['odpoved'])) { //odliseni verejne casti od rs
        if (isset ($_POST['zivotopis_nepovinny'])) { //soubor byl uz zadan, bylo pouzito zpet
            echo '<input type="hidden" name="zivotopis_nepovinny" value="'.$_POST['zivotopis_nepovinny'].'">';
            echo 'Soubor s Vaším životopisem byl již v pořádku uložen.';
        } 
        else { 

            if ($_FILES['fiadresa']['name']!='') { //kontrola zda byl zadan soubor ke stazeni
                if ($_FILES['fiadresa']['size']<=2000000 OR $_FILES['fiadresa']['size'] == 0) { //kontrola velikosti
                    //ziskani pripony
                    $name = $_FILES['fiadresa']['name'];
                    $typ = $_FILES['fiadresa']['type'];
                    $pripona = trim($name);
                    $ok = 0;
                    $tecka = mb_substr($pripona,-5,-4, "UTF-8");
                    if ($tecka == '.') {$pripona = mb_substr($pripona,-4,4,"UTF-8"); $ok=1;}
                    else {$tecka = mb_substr($pripona,-4,-3, "UTF-8");
                        if ($tecka == '.') {$pripona = mb_substr($pripona,-3,3,"UTF-8"); $ok=1;}
                        }
                    ///////////////// 
                    if (!$ok) {//nelze zjistit priponu
                       echo '<span class="chyba_staffer">Neznámý typ souboru, nelze přijmout!</span>';
                       $hodnoty[2] = 0;
                    }
                    else {
                      $pripona = mb_strtolower ($pripona,"UTF-8") ;
                      //jen nasledujici typy pripon
                            if ($pripona=='txt'  OR  
                                $pripona=='xls'  OR   $pripona=='doc' OR  $pripona=='xlsx'  OR   $pripona=='docx' OR
                                $pripona=='pdf'  OR   $pripona=='rtf' OR  $pripona=='odt'   OR   $pripona=='ods'  OR
                                $pripona=='ppt')
                            {
                                $id = uniqid(rand(), 0);
                                $nazev=$id.'.'.$pripona;
                                MySQL_Query("INSERT INTO staffer_zivotopisy (id,nazev,pripona,typ) VALUES ('$id','$nazev','$pripona','$typ')");
                                //MySQL_Query("INSERT INTO staffer_zivotopisy (id,nazev,pripona,typ) VALUES ('$id','$name','$pripona','$typ')");
                                $umisteni = 'rs/app/staffer/files/'.$id.'.'.$pripona;
                                //$umisteni = 'rs/app/staffer/files/'.$name;
                                $ok = move_uploaded_file($_FILES['fiadresa']['tmp_name'],$umisteni); chmod ($umisteni, 0777); 
                                if ($ok) {
                                        echo '<input type="hidden" name="zivotopis_nepovinny" value="'.$id.'">';
                                        echo '<span class="hodnota_staffer">'.$name.'</span>';
                                        }
                                else {
                                    echo '<span class="chyba_staffer">Soubor '.$name.' nebyl uložen!</span>';
                                }
                                
                                $hodnoty[1] = $id;

                            }
                    }


                    //move_uploaded_file($_FILES['fiadresa']['tmp_name'],$umisteni);
                    //chmod ($umisteni, 0777);
                    $hodnoty[2] = 1;
                }
                else {
                  //hlaseni o prekrocene velikosti
                  echo '<span class="chyba_staffer">Soubor s životopisem je příliš velký! Maximální velikost souboru je 1600kB.</span>';
                  $hodnoty[2] = 0;
                }
            }
            else {
            //hlasni o nevlozeni zivotopisu
               $hodnoty[2] = 1;
            }
        }
    } 
    /////////////////////////////////////////////////////////////////////// 
    else
    //Redakcni cast////////////////////////////////////////////////////////
    {
     if (isset($_GET['odpoved'])) {
                                   $id = $hodnoty[1];
                                   $vysledek = MySQL_Query("SELECT nazev FROM staffer_zivotopisy WHERE id='$id' LIMIT 1");
                                   $zaznam = MySQL_Fetch_Array($vysledek);
                                  
                                   //echo  "<br>zaznam-nazev*".   $zaznam['nazev'] . "<br>";
                                   
                                   $cesta = 'app/staffer/files/'.$zaznam['nazev'];
                                     if ($zaznam['nazev']) {
                                       echo 'Soubor se životopisem:<br>';
                                       echo '<a href="'.$cesta.'" target="_blank">STÁHNOUT</a>';
                                     }  
                                  }
    }
    ///////////////////////////////////////////////////////////////////////                      
  break;
default:
  if (isset ($_POST['zivotopis_nepovinny'])) {
      $hodnoty[2] = 1;
      
  }
  else {
       if ($krok != 3) {
           $hodnoty[2] = 0;
           if ($_FILES['fiadresa']['name']!='') {
               if ($_FILES['fiadresa']['size']<=2000000) {
                   $hodnoty[2] = 1;
               }
               else {;}
           }
           else {
             $hodnoty[2] = 1;
           }
       }
       else {
          $hodnoty[2] = 1;
       }
  }
 
  } // switch
 
 
  return $hodnoty;
} //function zivotopis_nepoviny
?>
