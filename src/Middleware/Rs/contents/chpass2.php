<?php
if ($sess_prava['newlist']) {
                 $okpass=1;
                 IF (isset($_POST['newpass1'])) {$newpass1 = $_POST['newpass1'];} else {$okpass=0;}
                 IF (isset($_POST['newpass2'])) {$newpass2 = $_POST['newpass2'];} else {$okpass=0;}
                 $newpass1=trim($newpass1);
                 $newpass1=strip_tags($newpass1);
                 $newpass1=quotemeta ($newpass1);
                 $newpass1=substr($newpass1,0,50);
                 IF (strlen($newpass1) < 5) {
                                   $okpass=0;
                                   echo '<p class=chyba>Vaše nové heslo je příliš krátké! Délka hesla musí být minimálně 5 znaků.</p>';
                 }
                 IF ($newpass1 === $newpass2 AND $okpass==1) {
                                   include 'data.inc.php';
                                   MySQL_Query("UPDATE opravneni SET password='$newpass1' WHERE user='$user'");  
                                   MySQL_CLOSE ($connect);
                                   echo '<p>Vaše heslo bylo změněno!</p>';
                                   include_once 'contents/0.php';
                 }
                 ELSE {
                                   $okpass=0;
                                   echo '<p class=chyba>Heslo se v obou jeho verzích neshoduje nebo obsahuje zakázané znaky!<br><i>Zakázány jsou všechny metaznaky.</i></p>';
                 }
                 IF (!$okpass) {include_once 'contents/chpass.php';}
} 
else {echo '<p class=chyba><br>Nemáte oprávnění k tomuto úkonu. </p>';}
?>


