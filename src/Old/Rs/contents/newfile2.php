<?php
$db_obsah = 'obsah_'.$lang;
if (isset($_POST['link_type'])) $linkType = $_POST['link_type'];
if (isset($_POST ['stranka'])) {$stranka = $_POST ['stranka'];} else {$stranka = 'x';};

if ($sess_prava[$stranka]) {
     if ($_FILES['fiadresa']['name'] != '') {
              $name = $_FILES['fiadresa']['name'];
              if ((isset($_POST['finazev'])) and ($_POST['finazev'] != '')) {
                     $finazev = $_POST['finazev'];}
              else {
                     $finazev = 'soubor '.$name;
              }
              $ok=1;
              $chunks = explode('.', trim($name));
              $pripona = end($chunks); // část řetězce za poslední tečkou
              if (!$pripona) $ok=0;
              if ($_FILES['fiadresa']['size'] == 0) {$ok=0;}

              if ($ok == "0") {
                 echo '<p class=chyba>Nesmyslná přípona souboru nebo je soubor příliš velký!</p>';
                 include 'contents/newfile.php';
              }
              else {

                  $typ = $_FILES['fiadresa']['type'];
                  $IDfile = uniqid(rand(), 0);
                  $IDfile = substr($IDfile,0,7);

                  $umisteni = Web\Middleware\Page\AppContext::getFilesDirectory().'files/'.$IDfile.'.'.$pripona;
//                  $umistenirs = '../rs/files/'.$IDfile.'.'.$pripona;
                  move_uploaded_file($_FILES['fiadresa']['tmp_name'],$umisteni);
                  chmod ($umisteni, 0777);
//                  copy ($umisteni,$umistenirs);
//                  chmod ($umistenirs, 0777);

                  //echo "<br>PRIPONA" . $pripona ;
                  if ($linkType == 'picture') {
                     if (($pripona=='JPEG') or ($pripona=='jpeg') or ($pripona=='jpg') or ($pripona=='JPG') or
                         ($pripona=='GIF') or ($pripona=='gif') or
                         ($pripona=='PNG') or ($pripona=='png')
                         )
                     {
                       $ok = 0;
                       $size = getimagesize ($umisteni);
                            //echo "<br>SIZE"; print_r($size);
                       if ($size[0] < 591) {$ok = 1;};
                       if ($size[1] > 500) {$ok = 0;};
                       if (($size[2] == 1) or ($size[2] == 2)  or ($size[2] == 3) ) {$ok = 1;} else {$ok = 0;};
                       if ($ok != 1) {
                               unlink ($umisteni);
                               echo '<p class=chyba>Tento obrázek má rozměry větší než 590x500 px nebo je komprimován nepodporovaným algoritmem!</p><br>';
                               include 'contents/newfile.php';
                               $ok=0;
                       }
                     }
                     else {
                               unlink ($umisteni);
                               echo '<p class=chyba>Tento formát obrázku není prohlížečem podporován!</p>';
                               include 'contents/newfile.php';
                               $ok=0;
                     }
                  }

                  IF ($ok != 0) {
                      include './data.inc.php';
                      MySQL_Query("INSERT INTO soubory (IDfile,list,lan,nazev,pripona,typ) VALUES ('$IDfile','$stranka','$lang','$finazev','$pripona','$typ')");
                      $vysledek = MySQL_Query("select $db_obsah from stranky where list='$stranka'");
                      $zaznam = MySQL_Fetch_Array($vysledek);
                      switch ($linkType) {
                          case 'picture':
                              $odkaz = '<img src="files/'.$IDfile.'.'.$pripona.'" alt="'.$finazev.'">';
                              break;
                          case 'view':
                              $odkaz = '<p><a href="files/'.$IDfile.'.'.$pripona.'" target="_blank">'.$finazev.'</a></p>';
                              break;
                          case 'download':
                              $odkaz = '<p>
                                          <a href="index.php?list=download&file='.$IDfile.'.'.$pripona.'" target="_blank">'.$finazev.'</a>
                                        </p>';
                              break;
                          default:
                              break;
                      }

                      $obsah = $zaznam[$db_obsah].$odkaz;  // vytvořený odkaz se připojí na konec obsahu stránky
                      MySQL_Query("update stranky set $db_obsah ='$obsah' where list = '$stranka'");
                      MySQL_CLOSE ($connect);

                      include 'contents/rl.php';
                  }
              }
      }
      else {echo '<p class=chyba>Nevybrali jste žádný soubor!</p>';
               include 'contents/newfile.php';
      }


}
else {echo '<p class=chyba>Nemáte oprávnění k importu!</p>';}

?>
