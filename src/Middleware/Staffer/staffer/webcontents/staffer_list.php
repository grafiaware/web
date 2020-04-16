<?php
if (isset($_GET['pozice'])) {$pozice = $_GET['pozice'];} else {$pozice = 0;}
//echo "*pozice*" . $pozice;

//echo $_GET['list']; echo "*list*" . $list;
//if (isset($_GET['list'])) {$list= $_GET['list']; } else ($list=0; )    //VSnemusi byt


$html_r='';
//$html_r .= html_echo( '<img src="rs/app/staffer/img/ikoclo16.gif" title="pozice_s_odmenou">&nbsp;&nbsp;&nbsp;
//           Tento symbol označuje pozici <b>s odměnou za zprostředkování</b>.');
$html_r .= html_echo('<div class="staffer_prehled_nadpis">Přehled volných pracovních pozic:</div>',0);

$html_r .= html_echo('<div class="staffer_div_tlacitko">');

if ($pozice === "vse") {
    $html_r .= html_echo( '<a href="?list='.  $list .'&pozice=0" class="staffer_tlacitko">Zavři vše</a>',0 );
}    
else {
    $html_r .= html_echo( '<a href="?list='.  $list .'&pozice=vse" class="staffer_tlacitko">Zobraz vše </a>',0 );
}
$html_r .= html_echo('</div>');

//$html_r .= html_echo( '<h2><a href="?list='.  $list .'&pozice=vse">Zobraz vše</a></h2>',0 );

include 'rs/data.inc.php';
// Lukáš - upravena část QUERY "(aktiv=2 AND (aktiv_start <= date(now())) AND (date(now()) <= aktiv_stop))" z porovnávání, 
// zda je now() menší než aktiv_start/stop na date(now()), protože aktiv_start/stop je DATE nikoliv DATETIME (což vrací fce now()) 
// a tím pádem to nefungovalo tak, jak by dle mě mělo.


$vysledek = MySQL_Query("SELECT id,nazev,pozice_s_odmenou,nabizime,pozadavky,popis,nastup,model FROM staffer_pozice WHERE aktiv=1 OR (aktiv=2 AND (aktiv_start <= date(now())) AND (date(now()) <= aktiv_stop)) ORDER BY nazev");
$vv=mysql_num_rows($vysledek);
if (!($vv)) {
    $html_r .= html_echo('<p>V tuto chvíli není vypsána žádná volná pracovní pozice.</p>',0) ;}

    
// Vypíšeme nejdřív jen tu vybranou pozici $pozice, 

$zvolena_pozice = MySQL_Query("SELECT id,nazev,pozice_s_odmenou,nabizime,pozadavky,popis,nastup,model FROM staffer_pozice WHERE id='$pozice' ORDER BY nazev");

while (@$zaznam_pozice = MySQL_Fetch_Array($zvolena_pozice)){
    $html_r .= html_echo( '<DIV class="staffer_zvolena_pozice">',0);


    $html_r .= html_echo( '<span class="staffer_zvolena_pozice_nadpis">'.$zaznam_pozice['nazev'].'</span>',0 ); 
    
   if ($zaznam_pozice['pozice_s_odmenou']) {   
       $html_r .= html_echo( '&nbsp;&nbsp;&nbsp;<a href="index.php?list=l22&language=lan1"><img src="rs/app/staffer/img/ikoclo16.gif" title="pozice_s_odmenou"></a>');    //IKONA
   }
   if ($zaznam_pozice['popis'])     {
                             $html_r .= html_echo( '<DIV class="staffer_blok_pozice">',0 );
                             $html_r .= html_echo( '<h3>Popis práce:</h3>',0 );
                             $html_r .= html_echo( '<p>'.$zaznam_pozice['popis'].'</p>',0 );
                             $html_r .= html_echo( '</DIV>',0 );
                             }
   if ($zaznam_pozice['pozadavky']) {
                             $html_r .= html_echo( '<DIV class="staffer_blok_pozice">',0 );
                             $html_r .= html_echo( '<h3>Požadujeme:</h3>',0 );
                             $html_r .= html_echo( '<p>'.$zaznam_pozice['pozadavky'].'</p>',0 );
                             $html_r .= html_echo( '</DIV>',0 );
                             }
   if ($zaznam_pozice['nabizime']) {
                             $html_r .= html_echo( '<DIV class="staffer_blok_pozice">',0);
                             $html_r .= html_echo( '<h3>Nabízíme:</h3>',0 );
                             $html_r .= html_echo( '<p>'.$zaznam_pozice['nabizime'].'</p>',0 );
                             $html_r .= html_echo( '</DIV>',0 );
                            }
   if ($zaznam_pozice['nastup']) {
                          $html_r .= html_echo( '<DIV class="staffer_blok_pozice">',0 );
                          $html_r .= html_echo( '<h3>Nástup:</h3>',0 );
                          $html_r .= html_echo( '<p>'.$zaznam_pozice['nastup'].'</p>',0 );
                          $html_r .= html_echo( '</DIV>',0 );
                          }
   if ($zaznam_pozice['model']) {
                         $html_r .= html_echo( '<DIV class="staffer_blok_pozice">',0 );
                         $html_r .= html_echo( '<h3>Směnný rozpis:</h3>',0 );
                         $html_r .= html_echo( '<p><a href="'.$zaznam_pozice['model'].'" target="_blank">otevřít</a></p>',0 );
                         $html_r .= html_echo( '</DIV>',0 );
                         }
//   $html_r .= html_echo( '<DIV class="staffer_blok_pozice">',0 );
   $html_r .= html_echo( '<a href="?list=' . konstStaffer_prihlasovaci_formular . '&pozice='. $zaznam_pozice['id']. '" class="staffer_tlacitko">Chci se přihlásit na pozici.</a>',0 );
   $html_r .= html_echo( '<a href="?list=' . konstStaffer_dotazovaci_formular . '&pozice='. $zaznam_pozice['id']. '" class="staffer_tlacitko">Požaduji informaci.</a>',0 );                                                 
//   $html_r .= html_echo( '</DIV>',0 );
   $html_r .= html_echo(  '</DIV>',0 );
}

// pak zbytek...
WHILE (@$zaznam_r = MySQL_Fetch_Array($vysledek)) {
    //echo "<br>*pozice*" . $pozice;
    //echo "*zaznam_r*" . $zaznam_r['id'];
    if($pozice != $zaznam_r[id]){
          
                if ($pozice === "vse") {
                   $html_r .= html_echo( '<DIV class="staffer_zvolena_pozice">',0);
                }
                
                $html_r .= html_echo( '<DIV class="staffer_pozice_odkaz">',0);
                $html_r .= html_echo( '<a href="?list='.  $list .'&pozice='.$zaznam_r['id'].'">'.$zaznam_r['nazev'].'</a>',0 );    

                if ($zaznam_r['pozice_s_odmenou']) {   
                        $html_r .= html_echo( '&nbsp;&nbsp;&nbsp;<a href="index.php?list=l22&language=lan1"><img src="rs/app/staffer/img/ikoclo16.gif" title="pozice_s_odmenou"></a>');     //IKONA
                }
                $html_r .= html_echo( '</DIV>',0);
                
                if ($pozice === "vse") {
                            if ($zaznam_r['popis'])     {
                                                        $html_r .= html_echo( '<DIV class="staffer_blok_pozice">',0 );
                                                        $html_r .= html_echo( '<h3>Popis práce:</h3>',0 );
                                                        $html_r .= html_echo( $zaznam_r['popis'],0 );
                                                        $html_r .= html_echo( '</DIV>',0 );
                                                        }
                            if ($zaznam_r['pozadavky']) {
                                                        $html_r .= html_echo( '<DIV class="staffer_blok_pozice">',0 );
                                                        $html_r .= html_echo( '<h3>Požadujeme:</h3>',0 );
                                                        $html_r .= html_echo( $zaznam_r['pozadavky'],0 );
                                                        $html_r .= html_echo( '</DIV>',0 );
                                                        }
                            if ($zaznam_r['nabizime']) {
                                                        $html_r .= html_echo( '<DIV class="staffer_blok_pozice">',0);
                                                        $html_r .= html_echo( '<h3>Nabízíme:</h3>',0 );
                                                        $html_r .= html_echo( $zaznam_r['nabizime'],0 );
                                                        $html_r .= html_echo( '</DIV>',0 );
                                                    }
                            
                            if ($zaznam_r['nastup']) {
                                                    $html_r .= html_echo( '<DIV class="staffer_blok_pozice">',0 );
                                                    $html_r .= html_echo( '<h3>Nástup:</h3>',0 );
                                                    $html_r .= html_echo( '<p>'.$zaznam_r['nastup'].'</p>',0 );
                                                    $html_r .= html_echo( '</DIV>',0 );
                                                    }
                            if ($zaznam_r['model']) {
                                                    $html_r .= html_echo( '<DIV class="staffer_blok_pozice">',0 );
                                                    $html_r .= html_echo( '<h3>Směnný rozpis:</h3>',0 );
                                                    $html_r .= html_echo( '<p><a href="'.$zaznam_r['model'].'" target="_blank">otevřít</a></p>',0 );
                                                    $html_r .= html_echo( '</DIV>',0 );
                                                    }
                            
                            $html_r .= html_echo( '<DIV class="staffer_blok_pozice">',0 );
                            
                            $html_r .= html_echo( '<a href="?list=' . konstStaffer_prihlasovaci_formular . '&pozice='. $zaznam_r['id']. '" class="staffer_tlacitko">Chci se přihlásit na pozici.</a>',0 );
                            $html_r .= html_echo( '<a href="?list=' . konstStaffer_dotazovaci_formular . '&pozice='. $zaznam_r['id']. '" class="staffer_tlacitko">Požaduji informaci.</a>',0 );
                            $html_r .= html_echo( '</DIV>',0 );
                            
                     $html_r .= html_echo( '</DIV>');        
                }  // if pozice === "vse"
                
               /*if ($pozice === "vse")  {
                       $html_r .= html_echo( '</DIV>');
                }*/
                                               
    } // if $pozice != $zaznam_r[id]
    
    
} // while
?>
