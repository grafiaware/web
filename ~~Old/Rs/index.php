<?php
if (isset($_GET['list']))   {
    $list = $_GET['list'];
} else {
    $list = '0';
}       // 0.php = help k RS

// $name - hodnota z $_GET se nikde nepoužívá
// $php - nebylo použito, smazal jsem, navíc je to nesmysl
//if (isset($_GET['name']))   {$name = $_GET['name'];}
//else                        {$name = "";}
//if (isset($_GET['php']))    {$php = $_GET['php'];}
//else                        {$php = '1';}         //apriory predpoklada .php skript

if (isset($_GET['stranka'])){
    $stranka = $_GET['stranka'];
} else {
    $stranka = 'x';
}
//{$stranka = 's';}


if (isset($_GET['lang']))  {
    $lang = $_GET['lang'];
} elseif (isset($_GET['language']))  {
    $lang = $_GET['language'];
} else {
    $lang = 'lan1';
}

if (isset($_GET['app']))    {
    $app=$_GET['app'];
}

//if (!isset($_GET['logout'])){$logout = 0;}
//else                        {$logout = $_GET['logout'];}

//if (isset($_GET['order'])) {$seznam_poradi = $_GET['order'];}   /*SEL pridano*/ // pro edun
//else                       {$seznam_poradi = false;}

//include 'app/app_konstanty.php';


include_once 'zobrazeni.inc.php';  /* definuje , ktere prvky budou - $zobraz_prvek*/

include 'lan_def.php';
if (!$zobraz_prvek['multi_lang']) {
    $lang = 'lan1';
}
 /*pozn.:  infonet je vzdy jen cesky- $zobraz_prvek['multi_lang']=false */


$menu_l='s';  /*leve*/    /*gr hlavni*/    /* oznaceni stranek - prislusnost k menu*/
$menu_p='p';  /*prave*/
$menu_s='p';  /*stredni*/ /*gr vodorovne*/
$menu_h='l';  /*horni*/   /*gr horni*/
$max_pocet_urovni_menu=5;  /*max pocet urovni menu - nelze vytvorit dalsi uroven pri zakladani stranky*/

//**************** je-li session otevrena a nechci pryc  - nastavi pristupOK =1 **********************
//if ((session_is_registered ("sess_user")) and ($logout == 0)) {$pristupOK = 1;}
if (isset ($_SESSION['security']['user'])) {
    $pristupOK = 1;
    $user = $_SESSION['security']['user']->getLoginAggregate();
}
if (isset($app) AND $app) {     // zvolena nová app - proměnná app v GET requestu - v nastroje.php vytvoren GET request - nastroje.php se volaly v cody/hlavicka.php - zakomentováno
    $_SESSION ["sess_app"] = $app;
} elseif (!isset($_SESSION ["sess_app"])) {
    $_SESSION ["sess_app"] = "rs";  // default
}
if (isset($user)) {
    $zaznam_opravneni = (new Model\Dao\OpravneniDao($mwContainer->get(Pes\Database\Handler\HandlerInterface::class)))->get($user);
    $sess_prava = $zaznam_opravneni;     // práva se načítala se za SESSION, teď je čtu vždy nově z databáze - platí tedy aktuální práva
}

if (isset($pristupOK)) {                      //****  pristup overen
    include 'activ_user.php';     //zapis do tab
    include "templates/index.php";
 }