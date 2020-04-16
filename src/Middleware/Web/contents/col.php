<?php
$files = scandir('kolaz');
$poc = count($files);
IF ($poc > 2) {
               $poc = $poc - 2;
               $cislo = rand (1,$poc);
               echo '<map name="Map">
                     <area href="index.php" shape="polygon" coords="0, 0, 420, 0, 420, 150, 0, 150" alt="Na titulní stránku">
                     <img src="kolaz/kol-'.$cislo.'.jpg" alt="Z golfového hřiště Golfklubu Klášter Teplá" usemap="#Map">
                    ';
              }
ELSE {echo 'Žádné koláže nejsou k dispozici...';}
?>
