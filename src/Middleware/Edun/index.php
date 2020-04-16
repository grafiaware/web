<?php
// nelezl jsem, že je třeba:
// $list,
// ??$app - tvoří se proměnná, ale asi se nepoužívá
// $order (dříve: hodnota se vloží do proměnné $seznam_porad- $_GET['order']

//include 'app/app_konstanty.php';


include_once 'zobrazeni.inc.php';  /* definuje , ktere prvky budou - $zobraz_prvek*/

//if ($user) {
//
//            //TODO: dočasně zablokováno!
//            assert(FALSE, "dočasně zablokováno!");
//
//    //TODO: Svoboda - přesunout do autorizačníhp middleware
//    include Middleware\Login\AppContext::getScriptsDirectory()."pristup.php";  //precte z tab. opravneni pro $user ,$zaznam_opravneni
//    $sess_prava = $zaznam_opravneni;     // práva se načítala se za SESSION, teď je čtu vždy nově z databáze - platí tedy aktuální práva
//}

//TODO: !! VŠICHNI VŠECHNO MŮŽOU!!
###################################


$sess_prava['role'] = "adm";

//if ($pristupOK) {                      //****  pristup overen
// TODO: Svoboda . v edunu se konflikty neřeší
//    include 'activ_user.php';     //zapis do tab
    include "templates/index.php";
// }