<?php
use Middleware\Rs\AppContext;

IF ($sess_prava['newlist']) {

IF (isset ($_GET['newstranka'])) {
                                                
  $newstranka=$_GET['newstranka'];
  $db_stranka = $newstranka{0};
  $db_nazev = 'nazev_'.$lang;
//////////////////
// Nova stranka //
////////////////// 
?>
  <H4>Přidání nové stránky do
<?php
  if ($zobraz_prvek['hlavni_menu'] )     {
       IF ($db_stranka == $menu_l) {echo 'hlavního menu</h4>';}  }
  if ($zobraz_prvek['vodo_menu'] )     {      
       IF ($db_stranka == $menu_s) {echo 'vodorovného menu</h4>';}  }
  if ($zobraz_prvek['horni_menu'] )     {
        IF ($db_stranka == $menu_h) {echo 'horního menu</h4>';}    }
  
  if ($zobraz_prvek['prave_menu'] )     {
        IF ($db_stranka == $menu_p ) {echo 'pravého menu</h4>';}    }
  if ($zobraz_prvek['leve_menu'] )     {      
        IF ($db_stranka == $menu_l ) {echo 'levého menu</h4>';}    }
                     

  //Definice menu
  $rozsah_urovne=3;//rika kolik znaku pripada na jednu uroven menu
  $defurovne = array (
                    0=>'class=polozka0',
                    1=>'class=polozka1',
                    2=>'class=polozka2',
                    3=>'class=polozka3',
                    4=>'class=polozka4',
                    5=>'class=polozka5'
                   ); //Definuje prislusne CSS k jednotlivym urovnim
  $uroven = ceil(strlen($newstranka)/$rozsah_urovne);
  $delka = $rozsah_urovne*$uroven;
  IF (strlen($newstranka) == 1) {$uroven = 0;}
  
  //Dotazy do DB na polozky jednotlivych urovni
  //Nacteni polozek prvni urovne (je zobrzena vzdy) do pole $data
  $handler = AppContext::getDb();
  $i = 0;
  $data = array (0=> array());
  $statement = $handler->query("SELECT list,$db_nazev,aut_gen FROM stranky
                        WHERE (left(list,1)='$db_stranka') AND
                              (char_length(list)='$rozsah_urovne') ORDER BY `poradi` ASC");
$statement->execute();
  
//  WHILE ($zaznam = MySQL_Fetch_Array($vysledek)) {
$zaznamy = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($zaznamy as $zaznan) {  
                    $data[0][$i]= array($zaznam['list'],$zaznam[$db_nazev], $zaznam['aut_gen']);
                    $i++;
  }
                                               
                                               
  //Kontrola a opatreni, zda existuje vubec nejaka polozka menu
  IF (count ($data[0])) {          //*** ano, existuji polozky menu
  echo '<p>Vyberte v menu níže položku, na jejíž úroveň chcete novou stránku umístit, nebo
       vyberte položku, ke které chcete vytvořit submenu.</p>
       <p><em>Ve vodorovných menu lze vytvářet pouze položky I. úrovně.</em></p>';
       


//Nacteni polozek dalsich urovni do pole $data
  IF ($uroven >= 1) {
  $i=1;
  WHILE ($i <= $uroven) {
                       $delkalist=($i+1)*$rozsah_urovne; 
                       $delkacastlist=$delkalist-$rozsah_urovne;
                       IF ($delkacastlist == 0) {$delkacastlist=$rozsah_urovne;}
                       $castlist=substr($newstranka, 0, $delkacastlist);
                       $data [$i]=array();
                       $j=0;
                       
                        $statement = $handler->query("SELECT list,$db_nazev,aut_gen FROM stranky WHERE (left(list,$delkacastlist)='$castlist') AND (char_length(list)='$delkalist') ORDER BY `poradi` ASC");
                        $statement->execute();

                        //  WHILE ($zaznam = MySQL_Fetch_Array($vysledek)) {
                        $zaznamy = $statement->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($zaznamy as $zaznan) {                         
                                    $data [$i][$j]= array($zaznam['list'],$zaznam[$db_nazev], $zaznam['aut_gen']);
                                    $j++;
                       }
                       $i++;
  }
  }

//Definice funkce rekurze menu
function vypis_urovne_menu_newlist ($i,$data,$rozsah_urovne,$newstranka,$lang,$defurovne,$sess_prava)
{
foreach ($data[$i] as $polozka) {
      IF ($sess_prava[$polozka[0]]) {
          $dovest='';
          /*if ($polozka[2] == 0) {$dovest='';}*/
          if (mb_substr($polozka[2],0,1) == 1) {$dovest='<span class=poznmenu><br>&nbsp;(publikace&nbsp;plán.kurzů)</span>';}
          if (mb_substr($polozka[2],0,1) == 2) {$dovest='<span class=poznmenu><br>&nbsp;(publikace&nbsp;katalog.kurzů)</span>';}
        
          IF ($newstranka == $polozka[0]) {
                echo '<a href="index.php?list=newlist2&newstranka='.$polozka[0].'&language='.$lang.'" '.$defurovne[$i].'on >'.$polozka[1]. $dovest .'</a>';
          }
          ELSE {echo '<a href="index.php?list=newlist2&newstranka='.$polozka[0].'&language='.$lang.'" '.$defurovne[$i].' >'.$polozka[1].  $dovest .'</a>';
          }
      }
      IF ($polozka[0] == substr($newstranka,0,$i*$rozsah_urovne+$rozsah_urovne)) {
             vypis_urovne_menu_newlist ($i+1,$data,$rozsah_urovne,$newstranka,$lang,$defurovne,$sess_prava);
      }
}                               
}
?>
  
  
  <DIV ID="rs_newlist_menu">
  <?php
  $i=0;
  vypis_urovne_menu_newlist ($i,$data,$rozsah_urovne,$newstranka,$lang,$defurovne,$sess_prava);
  ?>
  </DIV>

<DIV ID="newlist_form">
<form method="get" action="index.php">
<input type="hidden" name="list" value="newlist3">
<FIELDSET><LEGEND>Název nové stránky:</LEGEND>
<input maxlength="100" name="newnazev" size="33" type="text">
</FIELDSET>
<input type="hidden" name="newstranka" value="<?php echo $newstranka; ?>">
<br><FIELDSET><LEGEND>Pozice nové stránky:</LEGEND>
<input name="pozice" type="radio" value="0" checked>Stránku chci umístit na tuto úroveň<br>
<?php

  $delka_newstranka = strlen($newstranka);                                                       
  $delka_submenu = $delka_newstranka+$rozsah_urovne;
  IF ($rozsah_urovne > $delka_newstranka) {$i = 1;}
  ELSE {$vysledek = MySQL_Query("SELECT list FROM stranky            /*???????  */
                              WHERE (left(list,$delka_newstranka)='$newstranka') AND
                              (char_length(list)='$delka_submenu')");
        $i = mysql_num_rows ($vysledek);                            /*???????  */
  }
  MySQL_CLOSE ($connect);

  IF (/*!$i AND */($newstranka{0}!=$menu_s) AND ($newstranka{0}!=$menu_h)) {?>
      <input name="pozice" type="radio" value="1">Chci umístit stránku do submenu (tj.vytvořit submenu)
<?php
  }
?>  
</FIELDSET>

<?php
if ($db_stranka == $menu_l) {   //pro edun-publikace
  if (($sess_prava['role'] == 'adm') or ($sess_prava['role'] == 'sup')) { ?>
       <br><FIELDSET><LEGEND>Typ nové stránky:</LEGEND>
       <input name="publikace" type="radio" value="0" checked>Běžná stránka, tj. není určena pro publikaci kurzů<br>
       <!-- <input name="publikace" type="radio" value="1">Základní stránka pro publikaci - plánované kurzy<br> -->
       <input name="publikace" type="radio" value="2">Základní stránka pro publikaci - katalogové kurzy<br>
       </FIELDSET>
<?php
  }
}
?> 


<br><center><input value="Založit" type="submit"></center>
</form>
<?php
  IF ($zobraz_prvek['multi_lang'])  {
   echo '<p><i>Stránka bude založena i: <br>v <b>'.$lan2[2].'</b><br>a v <b>'.$lan3[2],'</b>.
   <br>pod stejným názvem, který jste zadali výše a bude ve stavu "neuveřejněno".</i></p>';
  }
  else {
    echo '<p><i>Stránka bude založena a bude ve stavu "neuveřejněno".</i></p>';
  }
?>
</DIV>
<?php
  }
  ELSE {                    /***  ne, neexistuji polozky menu */   ?>
        <form method="get" action="index.php">
        <input type="hidden" name="list" value="newlist3">
        <FIELDSET><LEGEND>Název nové stránky:</LEGEND>
        <input maxlength="100" name="newnazev" size="33" type="text">
        </FIELDSET>
        <input type="hidden" name="newstranka" value="<?php echo $newstranka; ?>">
        <br><center><input value="Založit" type="submit"></center>
        </form>
<?php
      IF (($lan2) OR ($lan3)) {
         echo '<p><i>Stránka bude založena i: <br>v <b>'.$lan2[2].'</b><br>a v <b>'.$lan3[2].'</b>.
         <br>Pod stejným názvem, který jste zadali výše a bude ve stavu "neuveřejněno".</i></p>';
      } 
     
  }

}
ELSE {
      echo '<p class=chyba>Nevybrali jste žádné z menu!</p>';
      include 'contents/newlist.php';
     }

}
else {
     echo '<p class=chyba><br>Nemáte oprávnění k tomuto úkonu.</p>';}?>
