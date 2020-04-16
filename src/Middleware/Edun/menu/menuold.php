<!--<DIV class=topmenu>-->
<!--</DIV>-->
<!--<DIV class=middmenu>-->
<!--bléé-->
<!--</DIV>-->
<!--<DIV class=menubott>-->
<!---->
<!--</DIV>-->
<DIV class=rs_topmenu>
</DIV>
<DIV class=rs_middmenu>

<?php
    if(mb_substr($list,0,13,"UTF-8") == 'katalog_kurzu') {
        echo '<a href="index.php?lang='.$lang.'&list=katalog_kurzu&app=edun" class=polozkaon>Katalog kurzů - kurzy</a>';}
    else{
        echo '<a href="index.php?lang='.$lang.'&list=katalog_kurzu&app=edun" class=polozka>Katalog kurzů - kurzy</a>';}

    
    if(mb_substr($list,0,5,"UTF-8") == 'casti') {
        echo '<a href="index.php?lang='.$lang.'&list=casti_kurzu&app=edun" class=polozkaon>Katalog kurzů - části</a>';}
    else{
        echo '<a href="index.php?lang='.$lang.'&list=casti_kurzu&app=edun" class=polozka>Katalog kurzů - části</a>';}          
    
        
    if(mb_substr($list,0,6,"UTF-8") == 'moduly') {
        echo '<a href="index.php?lang='.$lang.'&list=moduly_kurzu&app=edun" class=polozkaon>Katalog kurzů - moduly</a>';}
    else{
        echo '<a href="index.php?lang='.$lang.'&list=moduly_kurzu&app=edun" class=polozka>Katalog kurzů - moduly</a>';}      
    
    //if(mb_substr($list,0,14,"UTF-8") == 'katalog_publik') {
    //    echo '<a href="index.php?lang='.$lang.'&list=katalog_publikace&app=edun" class=polozkaon>Katalog kurzů - publikace</a>'; }
    //else{
    //    echo '<a href="index.php?lang='.$lang.'&list=katalog_publikace&app=edun" class=polozka>Katalog kurzů - publikace</a>';   }
    
    
        if(mb_substr($list,0,9,"UTF-8") == 'planovane') {
        echo '<a href="index.php?lang='.$lang.'&list=planovane_kurzy&app=edun" class=polozkaon>Plánované kurzy</a>';}
    else{
        echo '<a href="index.php?lang='.$lang.'&list=planovane_kurzy&app=edun" class=polozka>Plánované kurzy</a>';}
        
    
      
        
        
        
        
        
//IF (substr($list,0,7) == 'pormenu') {
//                                     echo '<a href="index.php?lang='.$lang.'&list=pormenu" class=polozkaon>Pořadí položek v menu</a>';
//                                    }
//ELSE {
//      echo '<a href="index.php?lang='.$lang.'&list=pormenu" class=polozka>Pořadí položek v menu</a>';
//     }
//IF (substr($list,0,7) == 'dellist') {
//                                     echo '<a href="index.php?lang='.$lang.'&list=dellist" class=polozkaon>Odstranit stránku</a>';
//                                    }
//ELSE {
//      echo '<a href="index.php?lang='.$lang.'&list=dellist" class=polozka>Odstranit stránku</a>';
//     }
?>
</DIV>
<DIV class=rs_menubott>
</DIV>
