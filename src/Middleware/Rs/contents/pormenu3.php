<?php
//print_r ($_POST);
if ($sess_prava['pormenu']) {
  include './data.inc.php';
  
  $klice = array_keys($_POST);
  IF (array_sum($_POST)) {
    foreach ($klice as $klic) {
                           
       if ((ctype_digit(@$_POST[$klic] )) and  (trim(@$_POST[$klic] != ""))   )  {
                              $dblist = $klic;
                              $poradi = $_POST[$klic];
                            MySQL_Query("update stranky set poradi='$poradi' where list = '$dblist'");
       }
    }
  
  /*pozn.  zada-li se jen jedno poradi a prave nulove, poradi se neulozi (neaktulazuje se na 0) */ 
  /* a program avizuje chybu v pormenu2. V kombinaci s jinymi poradi nenulovymi ulozi vse spravne */

MySQL_CLOSE ($connect);
include 'contents/pormenu2.php';
}
else {
      echo '<p class=chyba>Nemáte oprávnění k zápisu do databáze!</p>';
      include 'contents/pormenu2.php';
}
}
?>
