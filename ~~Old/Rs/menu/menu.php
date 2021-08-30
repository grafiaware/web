<?php

use Middleware\Rs\AppContext;

include_once 'l.php';
?>

<ul class="ui vertical menu">
    <li class="header item">Pomůcky a struktura</li>
    
    <li><a href="../index.php?list=<?php echo $stranka;?>&language=<?php echo $lang;?>" target="_blank" class="item">Zobrazit editovanou stránku v prohlížeči</a></li>

<?php
IF (substr($list,0,7) == 'newlist') {
      echo '<li class="selected"><a href="index.php?language='.$lang.'&list=newlist" class="item">Přidat novou stránku</a></li>';}
ELSE {
      echo '<li><a href="index.php?language='.$lang.'&list=newlist" class="item">Přidat novou stránku</a></li>';}

IF (substr($list,0,7) == 'pormenu') {
      echo '<li class="selected"><a href="index.php?language='.$lang.'&list=pormenu" class="item">Pořadí položek v menu</a></li>';}
ELSE {
      echo '<li><a href="index.php?language='.$lang.'&list=pormenu" class="item">Pořadí položek v menu</a></li>';}

IF (substr($list,0,7) == 'dellist') {
    echo '<li class="selected"><a href="index.php?language='.$lang.'&list=dellist" class="item">Odstranit stránku</a></li>';}
ELSE {
      echo '<li><a href="index.php?language='.$lang.'&list=dellist" class="item">Odstranit stránku</a></li>';}
?>
</ul>



<?php

$handler = AppContext::getDb();

$statement = $handler->query("SELECT list, nazev_lan1,obsah_lan1,aktiv_lan1, aktiv_lan1start, aktiv_lan1stop, keywords_lan1, aut_gen FROM stranky WHERE aut_gen='1'");
$statement->execute();
?>
<ul class="ui vertical menu">
    <li class="header item">Vygenerované</li>


    <li><em>Zde jsou k nahlédnutí části textů vygenerované v&nbsp;aplikaci Vzdělávací kurzy. (V&nbsp;editované stránce je použijte zápisem - např.:<br>--%HARMONOGRAM%--)</em></li>


<?php
//echo  "<br>" . $list;
//echo  "<br>" . $lan1;
while ($zaznam = $statement->fetch(PDO::FETCH_ASSOC)){
           // echo "<br>*" . $zaznam['list'] . "<br>";
      $E= "<li><a href='index.php?list=rl&stranka=" . $zaznam['list']. "&language=lan1' class='item'>" . $zaznam['list'] ."</a></li>" ;
      echo ($E);
}
?>

</ul>
