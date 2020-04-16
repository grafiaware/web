<?php
use Middleware\Rs\AppContext;

if ($sess_prava['dellist']) {
                             
IF (isset($_GET['delstranka'])) {
      $delstranka=$_GET['delstranka'];
      $db_stranka = $delstranka{0};
      $db_nazev = 'nazev_'.$lang; 
?>
  <h4>Průvodce odebráním stránky z 

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

  include 'data.inc.php';
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
  $uroven = ceil(strlen($delstranka)/$rozsah_urovne);
  $delka = $rozsah_urovne*$uroven;
  IF (strlen($delstranka) == 1) {$uroven = 0;}

  //Dotazy do DB na polozky jednotlivych urovni
  //Nacteni polozek prvni urovne (je zobrzena vzdy) do pole $data
  $handler = AppContext::getDb();
  $i = 0;
  $data = array (0=> array());
  $statement = $handler->query("SELECT list,$db_nazev,aut_gen FROM stranky WHERE (left(list,1)='$db_stranka') AND (char_length(list)='$rozsah_urovne') ORDER BY `poradi` ASC");
  $statement->execute();

//  WHILE ($zaznam = MySQL_Fetch_Array($vysledek)) {
  $zaznamy = $statement->fetchAll(PDO::FETCH_ASSOC);
  foreach ($zaznamy as $zaznan) {
      $data[0][$i]= array($zaznam['list'],$zaznam[$db_nazev],$zaznam['aut_gen']);
                             $i++;
  }
                             //Kontrola a opatreni, zda existuje vubec nejaka polozka menu
  IF (count ($data[0])) {
     echo '<p><p>Vyberte klikáním v menu pod tímto textem stránku, kterou chcete odebrat.</p>' .
          '<p><b>POZOR!</b> Pokud vyberete stránku, která obsahuje submenu, budou odstraněna i veškerá tato submenu včetně jejich obsahu!' .
          ' <br>Stránky, které budou odstraněny jsou označeny symbolem <img src="backgr/smazat.png"></p>';
     if ($zobraz_prvek['multi_lang'] )     {
       echo '<p><em>(Pozn.: Budou odstraněny i odpovídající stránky v ostatních jazykových verzích.)</em></p>' ;
     }     
    
     //Nacteni polozek dalsich urovni do pole $data
     IF ($uroven >= 1) {
     $i=1;
     WHILE ($i <= $uroven) {
       $delkalist=($i+1)*$rozsah_urovne; 
       $delkacastlist=$delkalist-$rozsah_urovne;
       IF ($delkacastlist == 0) {$delkacastlist=$rozsah_urovne;}
       $castlist=substr($delstranka, 0, $delkacastlist);
       $data [$i]=array();
       $j=0;
         $statement2 = $handler->query("SELECT list,$db_nazev,aut_gen FROM stranky WHERE (left(list,$delkacastlist)='$castlist') AND (char_length(list)='$delkalist') ORDER BY `poradi` ASC");
         $statement2->execute();
        //WHILE ($zaznam = MySQL_Fetch_Array($vysledek)) {
         $zaznamy2 = $statement2->fetchAll(PDO::FETCH_ASSOC);
        foreach ($zaznamy2 as $zaznan2) { 
                             $data [$i][$j]= array($zaznam2['list'],$zaznam2[$db_nazev],$zaznam2['aut_gen']);
                             $j++;
       }
       $i++;
     }
     }

//Definice funkce rekurze menu
function vypis_urovne_menu_dellist ($i,$data,$rozsah_urovne,$delstranka,$lang,$defurovne,$sess_prava){
  foreach ($data[$i] as $polozka) {
  IF ($sess_prava[$polozka[0]]) {
      $dovest=''; /*if ($polozka[2] == 0) {$dovest='';}*/
      if (mb_substr($polozka[2],0,1) == 1) {$dovest='<span class=poznmenu><br>&nbsp;(publikace&nbsp;plán.kurzů)</span>';}
      if (mb_substr($polozka[2],0,1) == 2) {$dovest='<span class=poznmenu><br>&nbsp;(publikace&nbsp;katalog.kurzů)</span>';}

      
     IF ($delstranka == $polozka[0] OR (strlen($delstranka) < strlen($polozka[0]) AND strlen($delstranka)>strlen($rozsah_urovne))) {
           echo '<a href="index.php?list=dellist2&delstranka='.$polozka[0].'&language='.$lang.'" '.$defurovne[$i].'on ><img src="backgr/smazat.png">'.$polozka[1]. $dovest .'</a>';
     }   
     ELSE {echo '<a href="index.php?list=dellist2&delstranka='.$polozka[0].'&language='.$lang.'" '.$defurovne[$i].' >'.$polozka[1]. $dovest .'</a>';}
  }
  IF ($polozka[0] == substr($delstranka,0,$i*$rozsah_urovne+$rozsah_urovne)) {
      vypis_urovne_menu_dellist ($i+1,$data,$rozsah_urovne,$delstranka,$lang,$defurovne,$sess_prava);
  }
  }                                
}
?>

<DIV ID="rs_dellist_menu">
<?php
     $i=0;
     vypis_urovne_menu_dellist ($i,$data,$rozsah_urovne,$delstranka,$lang,$defurovne,$sess_prava);
?>
</DIV>
<DIV ID="rs_dellist_form">
<?php
     IF (strlen($delstranka)>= $rozsah_urovne) {?>
     Po stisknutí tlačítka "Smazat" budou označené stránky <b>nenávratně</b> smazány!
     <FORM method="GET" action="index.php">
       <input type="hidden" name="list" value="dellist3">
       <input type="hidden" name="delstranka" value="<?php echo $delstranka;?>">
       <br><center><input value="Smazat" type="submit"></center>
     </FORM>
<?php ;}?>
</DIV>
                                                             
                                                             
                                                             
<?php }
ELSE {echo 'Menu neobsahuje žádné položky.';}             
}
ELSE {
echo '<p class=chyba>Nezadali jste menu, ve kterém chcete stránku odstranit!</p>';
include 'contents/dellist.php';
}
}
else {echo '<p class=chyba>Nemáte oprávnění k odstranění stránky!</p>';}

?>