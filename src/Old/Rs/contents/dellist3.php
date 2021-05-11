<?php
use Middleware\Rs\AppContext;

IF (isset ($_GET['delstranka'])) {
  $delstranka = $_GET['delstranka'];}

if ($sess_prava['dellist']) {
  
  $handler = AppContext::getDb();
  
  $statement = $handler->query("SELECT list,aut_gen FROM stranky WHERE (list= '$delstranka')");
    $statement->execute();
$zaz =  $statement->fetch(PDO::FETCH_ASSOC);
  
  if  ($zaz['aut_gen'] and ($sess_prava['role'] != 'adm') and ($sess_prava['role'] != 'sup') ) {
      echo '<p class=chyba><br>Nemáte oprávnění k odstranění publikační stránky. </p>';

  }
  else {
   
        IF ($use) {echo '<p class=chyba>Tuto stránku právě upravuje uživatel <b>'.$use.'</b></p><p>Stránku budete moci odstranit, až uživatel dokončí úpravy.</p><p><i>Pokud je stránka nedostupná i zjevně po ukončení práce ostatních uživatelů, pak se výše zmíněný uživatel neodhlásil ze systému. Stránka se odblokuje po 60-ti minutách.</i></p>';}
        ELSE {
                IF (isset ($_GET['delstranka'])) {
                         $delstranka = $_GET['delstranka'];
                }
                $rozsah_urovne=3;//rika kolik znaku pripada na jednu uroven menu
                IF ($rozsah_urovne <= strlen($delstranka)) {
                                                                          $delka=strlen($delstranka);
                        /* precte si vsechny stranky, ktere maji shodnou cast jmena (v delce jmena mazane stranky) s jmenem mazane stranky */
                        /* tedy vcetne podmenu */                                         
                                                                          $i=0;
                                                                          $data = array ();
                                                                          $statement2 = $handler->query("SELECT list FROM stranky WHERE (left(list,$delka)='$delstranka')");
                                                                          $statement2->execute();
//                                                                          WHILE ($zaznam = MySQL_Fetch_Array($vysledek)) {
                                                                            $zaznamy2 = $statement->fetchAll(PDO::FETCH_ASSOC);
                                                                            foreach ($zaznamy2 as $zaznan2) {
                                                                                                                          $dellist = $zaznam2['list'];
                                                                                                                          $data[$i] = $dellist;
                                                                                                                          $i++;
                                                                                                                          }
                                                                          foreach ($data as $dellist) {   /* A TADy SE TO SMAZE */
                                                                                                       include 'contents/delfilelist.php';
                                                                                                       $handler->exec("ALTER TABLE `opravneni` DROP $dellist");
                                                                                                       $handler->exec("DELETE FROM `stranky` WHERE CONVERT(`list` USING utf8) = '$dellist' LIMIT 1");
                                                                                                       //echo 'OK<br>';
                                                                                                       }
                                                                          $user = $_SESSION ["sess_user"];
                                                                          $statement3 = $handler->query("select * from `opravneni` where user='$user'");
                                                                          $statement3->execute();
                                                                          $zaznam_opravneni = $statement2->fetch(PDO::FETCH_ASSOC);
                                                                          $_SESSION ["sess_prava"] = $zaznam_opravneni;                   
                 }  ?>
                 
           <html>
            <head>
             <meta http-equiv="content-type" content="text/html; charset=utf-8">
             <meta http-equiv="refresh" content="0 ; URL=index.php">
            <title></title>
            </head>
            <body>
            </body>
            </html>
<?php

        }
  }     
}
else {echo '<p class=chyba><br>Nemáte oprávnění k odstranění stránky. </p>';}
?>
