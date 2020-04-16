<?php
include_once 'menu/l.php';
?>

<br/>
<DIV class=rs_topmenu>Pomůcky a struktura
</DIV>
<DIV class=rs_middmenu>
<a href="../index.php?list=<?php echo $stranka;?>&language=<?php echo $lang;?>" target="_blank" class=polozka>Zobrazit editovanou stránku v prohlížeči</a>
   
<?php
IF (substr($list,0,7) == 'newlist') {
      echo '<a href="index.php?lang='.$lang.'&list=newlist" class=polozkaon>Přidat novou stránku</a>';}
ELSE {
      echo '<a href="index.php?lang='.$lang.'&list=newlist" class=polozka>Přidat novou stránku</a>';}
      
IF (substr($list,0,7) == 'pormenu') {
      echo '<a href="index.php?lang='.$lang.'&list=pormenu" class=polozkaon>Pořadí položek v menu</a>';}
ELSE {
      echo '<a href="index.php?lang='.$lang.'&list=pormenu" class=polozka>Pořadí položek v menu</a>';}
      
IF (substr($list,0,7) == 'dellist') {
    echo '<a href="index.php?lang='.$lang.'&list=dellist" class=polozkaon>Odstranit stránku</a>';}
ELSE {
      echo '<a href="index.php?lang='.$lang.'&list=dellist" class=polozka>Odstranit stránku</a>';}
?>
</DIV>
<DIV class=rs_menubott>
</DIV>
