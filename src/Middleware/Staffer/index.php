<?php

// Pro PHP 5

if (isset($_GET['list']))   {$list = $_GET['list'];}
else                        {$list = '0';}       // 0.php = help k RS
//if (isset($_GET['name']))   {$name = $_GET['name'];}
//else                        {$name = "";}
//if (isset($_GET['php']))    {$php = $_GET['php'];}
//else                        {$php = '1';}         //apriory predpoklada .php skript
//if (isset($_GET['stranka'])){$stranka = $_GET['stranka'];}
//else                        {$stranka = 'x';}
   //{$stranka = 's';}


//if (isset($_GET['lang']))  {$lang = $_GET['lang'];}
//elseif (isset($_GET['language']))  {$lang = $_GET['language'];}
//else {$lang = 'lan1';}

if (isset($_GET['app']))    {$app=$_GET['app'];}
//if (!isset($_GET['logout'])){$logout = 0;}
//else                        {$logout = $_GET['logout'];}

if (isset($_GET['order'])) {$order = $_GET['order'];}   /*SEL pridano*/ // pro edun
else                       {$order = false;}



//include 'app/app_konstanty.php';


include_once 'zobrazeni.inc.php';  /* definuje , ktere prvky budou - $zobraz_prvek*/

//include 'lan_def.php';
//if (!$zobraz_prvek['multi_lang']) {$lang = 'lan1';}
 /*pozn.:  infonet je vzdy jen cesky- $zobraz_prvek['multi_lang']=false */


//$menu_l='s';  /*leve*/    /*gr hlavni*/    /* oznaceni stranek - prislusnost k menu*/
//$menu_p='p';  /*prave*/
//$menu_s='p';  /*stredni*/ /*gr vodorovne*/
//$menu_h='l';  /*horni*/   /*gr horni*/
//$max_pocet_urovni_menu=5;  /*max pocet urovni menu - nelze vytvorit dalsi uroven pri zakladani stranky*/

//**************** je-li session otevrena a nechci pryc  - nastavi pristupOK =1 **********************
//if ((session_is_registered ("sess_user")) and ($logout == 0)) {$pristupOK = 1;}
if (isset ($_SESSION['login']['user']) ) {
    $pristupOK = 1;
    $user = $_SESSION['login']['user'];

}
if (isset($app) AND $app) {     // zvolena nová app - v nastroje.php vytvoren GET request
    $_SESSION ["sess_app"] = $app;
} elseif (!isset($_SESSION ["sess_app"])) {
    $_SESSION ["sess_app"] = "staffer";  // default
    if ($list =='0') {$list = 'prehled_vsech_pozic';}
}

//// staffer
//// pokud ma uzivatel opravneni, pustit ho do stafferu v RS.
//if ($_SESSION ["sess_app"]=='staffer'){
//    IF ($sess_prava['staffer']) {
//    chdir("./app/staffer");
//    if ($list =='0') {$list = 'prehled_vsech_pozic';}
//    $name=$list.$pripona;
//    } else {echo '<p class="chyba">Nemáte dostatečná oprávnění ke vstupu na tuto stránku!</p>';}
//}// konec přidaného bloku o stafferu

if ($user) {
    //TODO: Svoboda - přesunout do autorizačníhp middleware
    include Middleware\Login\AppContext::getScriptsDirectory()."pristup.php";  //precte z tab. opravneni pro $user ,$zaznam_opravneni
    $sess_prava = $zaznam_opravneni;     // práva se načítala se za SESSION, teď je čtu vždy nově z databáze - platí tedy aktuální práva
}

if ($pristupOK) {                      //****  pristup overen
// TODO: Svoboda . v edunu se konflikty neřeší
//    include 'activ_user.php';     //zapis do tab
    include "templates/index.php";
 }