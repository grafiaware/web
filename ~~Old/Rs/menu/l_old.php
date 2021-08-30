<?php
//print_r ($_SESSION);
//$sess_prava = @$_SESSION ['sess_prava'];
$db_nazev = 'nazev_'.$lang;
$db_aktiv = 'aktiv_'.$lang;
$db_aktivstart = 'aktiv_'.$lang.'start';
$db_aktivstop = 'aktiv_'.$lang.'stop';
$db_stranka = $stranka{0};

include 'data.inc.php';

if  ($zobraz_prvek['multi_lang']) {
?>

<DIV class=topmenu>Výběr jazykové verze
</DIV>
<DIV class=middmenu>

<?php
IF ($lan1) {echo '<a href="index.php?language=lan1'; 
        IF ($list == 'stranky') {echo '&list=rl';} else {echo '&list='. $list;} /* rl */
        echo '&stranka='. $stranka . '"';
        IF ($lang=='lan1') {echo 'class=polozkaon>';} else {echo 'class=polozka>';}
        echo $lan1[1] . "</a>" ;
}
IF ($lan2) {echo '<a href="index.php?language=lan2';
        IF ($list == 'stranky') {echo '&list=rl';} else {echo '&list='.$list;} /* rl */
        echo '&stranka=' . $stranka . '"' ;
        IF ($lang=='lan2') {echo 'class=polozkaon>';} else {echo 'class=polozka>';}
        echo $lan2[1] . "</a>";
}
IF ($lan3) {echo '<a href="index.php?language=lan3';
        IF ($list == 'stranky') {echo '&list=rl';} else {echo '&list='.$list;}  /* rl */
        echo '&stranka=' . $stranka . '"';
        IF ($lang=='lan3') {echo 'class=polozkaon>';} else {echo 'class=polozka>';}
        echo $lan3[1] .  "</a>";
}
?>

</DIV>
<DIV class=menubott>
</DIV>
<br/>
<?php 
}
?>

<DIV class=topmenu>Výběr menu
</DIV>
<DIV class=middmenu>
                         
<?php
if ($zobraz_prvek['hlavni_menu'] ) { 
IF ($stranka{0} == $menu_l) {
    echo '<a href="index.php?language='.$lang.'&stranka=' . $menu_l . '" class=polozkaon>Stránky v hlavním menu</a>';}
ELSE {
    echo '<a href="index.php?language='.$lang.'&stranka=' . $menu_l . '" class=polozka>Stránky v hlavním menu</a>';}
}
if ($zobraz_prvek['horni_menu'] ) { 
IF ($stranka{0} == $menu_h) {
    echo '<a href="index.php?language='.$lang.'&stranka=' . $menu_h . '" class=polozkaon>Stránky v horním menu</a>';}
ELSE {
    echo '<a href="index.php?language='.$lang.'&stranka=' . $menu_h . '" class=polozka>Stránky v horním menu</a>'; }
}
if ($zobraz_prvek['vodo_menu'] ) {
IF ($stranka{0} == $menu_s) {
    echo '<a href="index.php?language='.$lang.'&stranka=' . $menu_s . '" class=polozkaon>Stránky ve vodorovném menu</a>'; }
ELSE {
    echo '<a href="index.php?language='.$lang.'&stranka=' . $menu_s . '" class=polozka>Stránky ve vodorovném menu</a>';}
}    

if ($zobraz_prvek['leve_menu'] ) {
IF ($stranka{0} == $menu_l ) {
      echo '<a href="index.php?language='.$lang.'&stranka=' . $menu_l . '" class=polozkaon>Stránky v levém menu</a>';}
ELSE {
      echo '<a href="index.php?language='.$lang.'&stranka=' . $menu_l . '" class=polozka>Stránky v levém menu</a>';}
}
if ($zobraz_prvek['prave_menu'] ) {
IF ($stranka{0} == $menu_p ) {
      echo '<a href="index.php?language='.$lang.'&stranka=' . $menu_p .'" class=polozkaon>Stránky v pravém menu</a>';}
ELSE {
      echo '<a href="index.php?language='.$lang.'&stranka='. $menu_p . '" class=polozka>Stránky v pravém menu</a>';}
}


?>
</DIV>
<DIV class=menubott>
</DIV>


<br/>
<DIV class=topmenu>Obsah stránek
</DIV>
<DIV class=middmenu>
<?php
/////////////////////
// Nova verze menu //
/////////////////////                      

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
$uroven = ceil(strlen($stranka)/$rozsah_urovne);
$delka = $rozsah_urovne*$uroven;
IF (strlen($stranka) == 1) {$uroven = 0;}
//Dotazy do DB na polozky jednotlivych urovni
//Nacteni polozek prvni urovne (je zobrzena vzdy) do pole $data
$i = 0;
$data = array (0=> array());
$vysledek = MySQL_Query("SELECT list,$db_nazev,$db_aktiv,$db_aktivstart,$db_aktivstop,aut_gen FROM stranky
                        WHERE (left(list,1)='$db_stranka') AND
                              (char_length(list)='$rozsah_urovne') ORDER BY `poradi` ASC");
WHILE ($zaznam = MySQL_Fetch_Array($vysledek)) {
        $data[0][$i]= array($zaznam['list'],
                            $zaznam[$db_nazev],
                            $zaznam[$db_aktiv],
                            $zaznam[$db_aktivstart],
                            $zaznam[$db_aktivstop],
                            $zaznam['aut_gen'],
                            );
        $i++;
}

//Nacteni polozek dalsich urovni do pole $data
IF ($uroven >= 1) {
$i=1;
WHILE ($i <= $uroven) {
        $delkalist=($i+1)*$rozsah_urovne; 
        $delkacastlist=$delkalist-$rozsah_urovne;
        IF ($delkacastlist == 0) {$delkacastlist=$rozsah_urovne;}
        $castlist=substr($stranka, 0, $delkacastlist);
        $data [$i]=array();
        $j=0;
        $vysledek = MySQL_Query("SELECT list,$db_nazev,$db_aktiv,$db_aktivstart,$db_aktivstop,aut_gen FROM stranky WHERE (left(list,$delkacastlist)='$castlist') AND (char_length(list)='$delkalist') ORDER BY `poradi` ASC");
        WHILE ($zaznam = MySQL_Fetch_Array($vysledek)) {
            //echo 'yaynam ' ; print_r($zaznam);
              $data [$i][$j]= array($zaznam['list'],
                                    $zaznam[$db_nazev],
                                    $zaznam[$db_aktiv],
                                    $zaznam[$db_aktivstart],
                                    $zaznam[$db_aktivstop],
                                    $zaznam['aut_gen']);
              $j++;
        }
        $i++;
}
}

//Definice funkce rekurze menu
function vypis_urovne_menu ($i,$data,$rozsah_urovne,$stranka,$lang,$defurovne,$sess_prava)
{
foreach ($data[$i] as $polozka) {
       IF (@$sess_prava[$polozka[0]]) {
            IF ($polozka[2]){$navest='<img src="backgr/zelena.gif">';}
            IF (!$polozka[2]){$navest='<img src=<?= Middleware\Rs\AppContext::getAppPublicDirectory().'grafia/img/cervena.gif'?>>';}
            IF ($polozka[2] == 2) {
                         IF (strtotime($polozka[3]) <= time() AND time() <= strtotime($polozka[4]." + 23 hours 59 minutes 59 seconds")) {
                                                  $navest='<img src="backgr/zelena.gif">';}
                         ELSE {$navest='<img src=<?= Middleware\Rs\AppContext::getAppPublicDirectory().'grafia/img/cervena.gif'?>>';}
            }
            
            $dovest='';/*if ($polozka[5] == 0) {$dovest='';}*/
            if (mb_substr($polozka[5],0,1) == 1) {$dovest='<span class=poznmenu><br>&nbsp;(publikace&nbsp;plán.kurzů)</span>';}
            if (mb_substr($polozka[5],0,1) == 2) {$dovest='<span class=poznmenu><br>&nbsp;(publikace&nbsp;katalog.kurzů)</span>';}
            IF ($stranka == $polozka[0]) {
                  echo '<a href="index.php?list=rl&stranka='.$polozka[0].'&language='.$lang.'" '.$defurovne[$i].'on >'.$navest.$polozka[1]. $dovest .'</a>';
            }
            ELSE {echo '<a href="index.php?list=rl&stranka='.$polozka[0].'&language='.$lang.'" '.$defurovne[$i].' >'.$navest.$polozka[1]. $dovest . '</a>';}
       }
       IF ($polozka[0] == substr($stranka,0,$i*$rozsah_urovne+$rozsah_urovne)) {
                         vypis_urovne_menu ($i+1,$data,$rozsah_urovne,$stranka,$lang,$defurovne,$sess_prava);
       }
}                               
}

$i=0;
vypis_urovne_menu ($i,$data,$rozsah_urovne,$stranka,$lang,$defurovne,$sess_prava);
?>

</DIV>
<DIV class=menubott>
</DIV>




<br/>
<DIV class=topmenu>Další stránky
</DIV>
<DIV class=middmenu>
<?php
//Dotaz a zobrazeni odkazu na uverejnene stranky typu a
$vysledek = MySQL_Query("SELECT list,$db_nazev FROM stranky WHERE (left(list,1)='a') AND (($db_aktiv=1) OR (($db_aktiv=2) AND ($db_aktivstart <= now()) AND (now() <= $db_aktivstop))) ORDER BY `poradi` ASC");                                          
while ($zaznam = MySQL_Fetch_Array($vysledek)){
                $dblist = $zaznam['list'];
                $dbnazev = $zaznam[$db_nazev];
                $menu = substr($dblist,0,1);
                if ($sess_prava[$dblist]) {?>
                         <a href="index.php?list=rl&stranka=<?php echo $dblist;?>&language=<?php echo $lang;?>"<?php if ($stranka == $dblist) {echo 'class=polozkaon';} else {echo 'class=polozka';}?>><img src="backgr/zelena.gif"><?php echo $dbnazev;?></a>
<?php ;}
                                                                                  
                                              } 
//Dotaz a zobrazeni odkazu na neuverejnene stranky typu a
$vysledek = MySQL_Query("SELECT list,$db_nazev FROM stranky WHERE (left(list,1)='a') AND (($db_aktiv=0) OR (($db_aktiv=2) AND (($db_aktivstart > now()) OR (now() > $db_aktivstop)))) ORDER BY `poradi` ASC");                                          
while ($zaznam = MySQL_Fetch_Array($vysledek)){
                                               $dblist = $zaznam['list'];
                                               $dbnazev = $zaznam[$db_nazev];
                                               $menu = substr($dblist,0,1);
                                               if ($sess_prava[$dblist]) {?>
                         <a href="index.php?list=rl&stranka=<?php echo $dblist;?>&language=<?php echo $lang;?>"<?php if ($stranka == $dblist) {echo 'class=polozkaon';} else {echo 'class=polozka';}?>><img src=<?= Middleware\Rs\AppContext::getAppPublicDirectory().'grafia/img/cervena.gif'?>><?php echo $dbnazev;?></a>
                                                                                      <?php ;}
                                                                                  
                                              }


/*  
//Dotaz a zobrazeni odkazu na uverejnene stranky typu kk
$vysledek = MySQL_Query("SELECT list,$db_nazev FROM stranky WHERE (left(list,2)='kk') AND (($db_aktiv=1) OR (($db_aktiv=2) AND ($db_aktivstart <= now()) AND (now() <= $db_aktivstop))) ORDER BY `poradi` ASC");                                          
while ($zaznam = MySQL_Fetch_Array($vysledek)){
                $dblist = $zaznam['list'];
                $dbnazev = $zaznam[$db_nazev];
                $menu = substr($dblist,0,1);
                if ($sess_prava[$dblist]) {?>
                         <a href="index.php?list=rl&stranka=<?php echo $dblist;?>&language=<?php echo $lang;?>"<?php if ($stranka == $dblist) {echo 'class=polozkaon';} else {echo 'class=polozka';}?>><img src="backgr/zelena.gif"><?php echo $dbnazev;?></a>
<?php ;}
                                                                                  
                                              }   
                                              
//Dotaz a zobrazeni odkazu na neuverejnene stranky typu kk
$vysledek = MySQL_Query("SELECT list,$db_nazev FROM stranky WHERE (left(list,2)='kk')
                        AND (($db_aktiv=0) OR (($db_aktiv=2) AND (($db_aktivstart > now()) OR (now() > $db_aktivstop)))) ORDER BY `poradi` ASC");                                          
while ($zaznam = MySQL_Fetch_Array($vysledek)){
                                               $dblist = $zaznam['list'];
                                               $dbnazev = $zaznam[$db_nazev];
                                               $menu = substr($dblist,0,1);
                                               if ($sess_prava[$dblist]) {?>
                         <a href="index.php?list=rl&stranka=<?php echo $dblist;?>&language=<?php echo $lang;?>"<?php if ($stranka == $dblist) {echo 'class=polozkaon';} else {echo 'class=polozka';}?>><img src=<?= Middleware\Rs\AppContext::getAppPublicDirectory().'grafia/img/cervena.gif'?>><?php echo $dbnazev;?></a>
                                                                                      <?php ;}
                                                                                  
                                              }
  */                                              
                                              
                                              
                                              
 ?>
 
</DIV>
<DIV class=menubott>
</DIV>

<?php 
//--------------------------------------------------------------------------------------
MySQL_CLOSE ($connect);
?>
