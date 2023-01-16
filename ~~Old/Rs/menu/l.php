<?php

use Middleware\Rs\AppContext;

//print_r ($_SESSION);
//$sess_prava = @$_SESSION ['sess_prava'];
$db_nazev = 'nazev_'.$lang;
$db_aktiv = 'aktiv_'.$lang;
$db_aktivstart = 'aktiv_'.$lang.'start';
$db_aktivstop = 'aktiv_'.$lang.'stop';
$db_stranka = $stranka{0};

if  ($zobraz_prvek['multi_lang']) {
?>

<!--<DIV class=rs_topmenu>Výběr jazykové verze
</DIV>
<DIV class=rs_middmenu>-->
<ul class="ui vertical menu">
    <li class="header item">Výběr jazykové verze</li>
    <li class="item">

<?php

IF ($lan1) {echo '<a href="index.php?language=lan1';
        IF ($list == 'stranky') {echo '&list=rl';} else {echo '&list='. $list;} /* rl */
        echo '&stranka='. $stranka . '"';
        IF ($lang=='lan1') {echo 'class="jazyk-on">';} else {echo 'class="jazyk-off">';} //{echo 'class=polozkaon>';} else {echo 'class=polozka>';}
        echo "<img src='".Middleware\Rs\AppContext::getAppPublicDirectory()."/grafia/img/cze.gif'/> </a>" ; //echo $lan1[1] . "</a>" ;
}
IF ($lan2) {echo '<a href="index.php?language=lan2';
        IF ($list == 'stranky') {echo '&list=rl';} else {echo '&list='.$list;} /* rl */
        echo '&stranka=' . $stranka . '"' ;
        IF ($lang=='lan2') {echo 'class="jazyk-on">';} else {echo 'class="jazyk-off">';} //{echo 'class=polozkaon>';} else {echo 'class=polozka>';}
        echo "<img src='".Middleware\Rs\AppContext::getAppPublicDirectory()."/grafia/img/eng.gif'/> </a>";  //echo $lan2[1] . "</a>";
}
IF ($lan3) {echo '<a href="index.php?language=lan3';
        IF ($list == 'stranky') {echo '&list=rl';} else {echo '&list='.$list;}  /* rl */
        echo '&stranka=' . $stranka . '"';
        IF ($lang=='lan3') {echo 'class="jazyk-on">';} else {echo 'class="jazyk-off">';} //{echo 'class=polozkaon>';} else {echo 'class=polozka>';}
        echo "<img src='".Middleware\Rs\AppContext::getAppPublicDirectory()."/grafia/img/ger.gif'/> </a>";  //echo $lan3[1] .  "</a>";
}

//echo "</DIV>
//<DIV class=rs_menubott>
//</DIV>
//<br/>";
?>
    </li>
</ul>
<?php
}
//echo "
//<DIV class=rs_topmenu>Výběr menu
//</DIV>
//<DIV class=rs_middmenu>";
echo '<ul class="ui vertical menu">
        <li class="header item">Výběr menu</li>';

if ($zobraz_prvek['hlavni_menu'] ) {
IF ($stranka{0} == $menu_l) {
    echo '<li class="selected"><a href="index.php?language='.$lang.'&stranka=' . $menu_l . '" class="item">Stránky v hlavním menu</a></li>';}
ELSE {
    echo '<li><a href="index.php?language='.$lang.'&stranka=' . $menu_l . '" class="item">Stránky v hlavním menu</a></li>';}
}
if ($zobraz_prvek['horni_menu'] ) {
IF ($stranka{0} == $menu_h) {
    echo '<li class="selected"><a href="index.php?language='.$lang.'&stranka=' . $menu_h . '" class="item">Stránky v horním menu</a></li>';}
ELSE {
    echo '<li><a href="index.php?language='.$lang.'&stranka=' . $menu_h . '" class="item">Stránky v horním menu</a></li>'; }
}
if ($zobraz_prvek['vodo_menu'] ) {
IF ($stranka{0} == $menu_s) {
    echo '<li class="selected"><a href="index.php?language='.$lang.'&stranka=' . $menu_s . '" class="item">Stránky ve vodorovném menu</a></li>'; }
ELSE {
    echo '<li><a href="index.php?language='.$lang.'&stranka=' . $menu_s . '" class="item">Stránky ve vodorovném menu</a></li>';}
}

if ($zobraz_prvek['leve_menu'] ) {
IF ($stranka{0} == $menu_l ) {
      echo '<li class="selected"><a href="index.php?language='.$lang.'&stranka=' . $menu_l . '" class="item">Stránky v levém menu</a></li>';}
ELSE {
      echo '<li><a href="index.php?language='.$lang.'&stranka=' . $menu_l . '" class="item">Stránky v levém menu</a></li>';}
}
if ($zobraz_prvek['prave_menu'] ) {
IF ($stranka{0} == $menu_p ) {
      echo '<li class="selected"><a href="index.php?language='.$lang.'&stranka=' . $menu_p .'" class="item">Stránky v pravém menu</a></li>';}
ELSE {
      echo '<li><a href="index.php?language='.$lang.'&stranka='. $menu_p . '" class="item">Stránky v pravém menu</a></li>';}
}

//echo "
//</DIV>
//<DIV class=rs_menubott>
//</DIV>
//
//<br/>
//<DIV class=rs_topmenu>Obsah stránek
//</DIV>
//<DIV class=rs_middmenu>

echo '</ul>' ;
echo '<ul class="ui vertical menu">
        <li class="header item">Obsah stránek</li>';

/////////////////////
// Nova verze menu //
/////////////////////

//Definice menu
$rozsah_urovne=3;//rika kolik znaku pripada na jednu uroven menu
$defurovne = array (
//                    0=>'class=polozka0',
//                    1=>'class=polozka1',
//                    2=>'class=polozka2',
//                    3=>'class=polozka3',
//                    4=>'class=polozka4',
//                    5=>'class=polozka5'
                    0=>'class="item"',
                    1=>'class="item"',
                    2=>'class="item"',
                    3=>'class="item"',
                    4=>'class="item"',
                    5=>'class="item"'
                   ); //Definuje prislusne CSS k jednotlivym urovnim
$uroven = ceil(strlen($stranka)/$rozsah_urovne);
$delka = $rozsah_urovne*$uroven;
IF (strlen($stranka) == 1) {$uroven = 0;}
//Dotazy do DB na polozky jednotlivych urovni
//Nacteni polozek prvni urovne (je zobrzena vzdy) do pole $data

$handler = AppContext::getDb();

$i = 0;
$data = array (0=> array());
$statement = $handler->query("SELECT list,$db_nazev,$db_aktiv,$db_aktivstart,$db_aktivstop,aut_gen FROM stranky
                        WHERE (left(list,1)='$db_stranka') AND
                              (char_length(list)='$rozsah_urovne') ORDER BY `poradi` ASC");
$statement->execute();
WHILE ($zaznam = $statement->fetch(PDO::FETCH_ASSOC)) {
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
        $statement = $handler->query("SELECT list,$db_nazev,$db_aktiv,$db_aktivstart,$db_aktivstop,aut_gen FROM stranky WHERE (left(list,$delkacastlist)='$castlist') AND (char_length(list)='$delkalist') ORDER BY `poradi` ASC");
        $statement->execute();
        WHILE ($zaznam = $statement->fetch(PDO::FETCH_ASSOC)) {
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
        if (isset($sess_prava[$polozka[0]]) AND $sess_prava[$polozka[0]]) {
            if ($polozka[2]==1 OR ($polozka[2]==2 AND strtotime($polozka[3]) <= time() AND time() <= strtotime($polozka[4]." + 23 hours 59 minutes 59 seconds"))) {
                $navest='<img src="'.Middleware\Rs\AppContext::getAppPublicDirectory().'grafia/img/zelena.gif">';
            } else {
                $navest='<img src="'.Middleware\Rs\AppContext::getAppPublicDirectory().'grafia/img/cervena.gif">';
            }

            $dovest='';/*if ($polozka[5] == 0) {$dovest='';}*/
            if (mb_substr($polozka[5],0,1) == 1) {
                $dovest='<span class=poznmenu><br>&nbsp;(publikace&nbsp;plán.kurzů)</span>';
            }
            if (mb_substr($polozka[5],0,1) == 2) {
                $dovest='<span class=poznmenu><br>&nbsp;(publikace&nbsp;katalog.kurzů)</span>';
            }
            IF ($stranka == $polozka[0]) {
                echo '<li class="selected"><a href="index.php?list=rl&stranka='.$polozka[0].'&language='.$lang.'" '.$defurovne[$i].' >'.$navest.$polozka[1]. $dovest .'</a>';
                //echo '<a href="index.php?list=rl&stranka='.$polozka[0].'&language='.$lang.'" '.$defurovne[$i].'on >'.$navest.$polozka[1]. $dovest .'</a>';
            } else {
                echo '<li><a href="index.php?list=rl&stranka='.$polozka[0].'&language='.$lang.'" '.$defurovne[$i].' >'.$navest.$polozka[1]. $dovest . '</a>';
                //echo '<a href="index.php?list=rl&stranka='.$polozka[0].'&language='.$lang.'" '.$defurovne[$i].' >'.$navest.$polozka[1]. $dovest . '</a>';
            }
        }
        if ($polozka[0] == substr($stranka,0,$i*$rozsah_urovne+$rozsah_urovne)) {
            echo '<ul class="menu">';
                vypis_urovne_menu ($i+1,$data,$rozsah_urovne,$stranka,$lang,$defurovne,$sess_prava);
            echo '</ul>';
        }
    }
}

$i=0;
vypis_urovne_menu ($i,$data,$rozsah_urovne,$stranka,$lang,$defurovne,$sess_prava);
?>

</ul>

<ul class="ui vertical menu">
    <li class="header item">Další stránky</li>
<?php

//Dotaz a zobrazeni menu stranek s prefixem a
$menuDao = new Model\Dao\MenuDao($mwContainer->get(Pes\Database\Handler\HandlerInterface::class), 3);
$zaznamy = $menuDao->findChildrenNodes($lang, 'a', 2);   // a0, a1 atd. -> 'a' stránky mají délku identifikátoru 2 znaky na úrověň
$menuDao = new Model\Dao\Hierarchy\ReadHierarchy($handler, $nestedSetTableName, $itemTableName);

        foreach($menuDao->getImmediateSubNodes($langCode, $parentUid, $active, $actual) as $row) {

//Dotaz a zobrazeni odkazu na uverejnene stranky typu a
foreach($zaznamy as $zaznam) {
    if ($zaznam[$menuDao->dbAktivName($lang)]==1) {
        $circleImage = 'zelena.gif';
    } elseif($zaznam[$menuDao->dbAktivName($lang)]==2 AND $zaznam['aktual']) {
        $circleImage = 'zelenocervena.gif';
    } elseif($zaznam[$menuDao->dbAktivName($lang)]==2 AND !$zaznam['aktual']) {
        $circleImage = 'oranzova.gif';
    } else {
        $circleImage = 'cervena.gif';
    }
    if ($sess_prava[$zaznam['list']]) {
        echo "<a href='index.php?".http_build_query(['list'=>'rl', 'stranka'=>$zaznam['list'], 'language'=>$lang])."'"
            .(($stranka==$zaznam['list']) ? " class='polozkaon'" : " class='polozka'").">"
            ."<img src='".Middleware\Rs\AppContext::getAppPublicDirectory().'grafia/img/'.$circleImage."'>{$zaznam[$menuDao->dbNazevName($lang)]}</a>";
    }
}
?>
</ul>